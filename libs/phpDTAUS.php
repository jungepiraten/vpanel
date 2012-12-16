<?php
/**
 * phpDTAUS helps you creating so-called DTAUS (Datenaustauschverfahren) files. This file format
 * was devised by the German ZKA or Zentraler Kreditausschuss (Central Credit Committee) in the
 * mid-seventies. It is used for the automated processing of wire transfers and direct debits in
 * Germany. The files created with this class are either sent to the bank (via email or any other
 * means) or imported into online banking software for processing.
 * 
 * To check the validity of the file you may visit http://www.xpecto.de/content/dtauschecker. There
 * you simply upload the created file. If everything is alright, you should get a message that the
 * file is valid, as well as a list of the transactions that were encoded in the file.
 * 
 * @author Alexander Serbe <alexander.serbe@progressive-dt.com>
 * @version 1.0
 * @copyright Copyright 2012 by progressive design and technology
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 * 
 * ------------------------------------------------------------------------------------------------
 * 
 * phpDTAUS 1.0
 * Copyright (c) 2010 by progressive design and technology
 * 
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License along with this
 * program.  If not, see <http://www.gnu.org/licenses/>.
 */
class phpDTAUS
{
    /**
     * Array of checksums for account numbers, bank codes, and amounts. These values are added to
     * the footer of the DTAUS file to verify its content.
     * @var array $_checksums
     */
    protected $_checksums = array(
        'accountNumbers' => 0,
        'bankCodes' => 0,
        'amounts' => 0
    );
    
    /**
     * Default mapping for CSV files. This mapping is used, if none is specified in the call to
     * readCsv().
     * @var array $_csvDefaultMapping
     */
    protected $_csvDefaultMapping = array(
        'name' => 0,
        'bankCode' => 1,
        'account' => 2,
        'amount' => 3,
        'ref' => 4,
        'type' => 5,
        'customerId' => 6
    );
    
    /**
     * Array of formatted C-records. C-records in a DTAUS file contain the actual transactions.
     * @var array $_entries
     */
    protected $_entries = array();
    
    /**
     * Array of C-record extensions. Every C-record may contain a certain number of extensions.
     * These extensions may contain additional information on the beneficiary of the payment,
     * the reference and the payer. Please note that only a specified number for each type of
     * extension are allowed:
     *     + max. 1 extension for the beneficiary,
     *     + max. 13 extensions for the reference, and
     *     + max. 1 extension for the payer
     */
    protected $_extensions = array();
    
    /**
     * Account number of the originator.
     * @var integer $_originatorAccount
     */
    protected $_originatorAccount;
    
    /**
     * Bank code of the originator's bank.
     * @var integer $_originatorBankCode
     */
    protected $_originatorBankCode;
    
    /**
     * Name of the originator.
     * @var string $_originatorName
     */
    protected $_originatorName;
    
    protected $_type;
    
    /**
     * Constructor. Checks input parameters and stores them in the appropriate member variables.
     * Throws an InvalidArgumentException in case one or more parameters do not meet the given
     * specifications.
     * 
     * @return void
     * @throws InvalidArgumentException
     * @param string $originatorName Name of the originator
     * @param integer $originatorBankCode Bank code of the originator's bank
     * @param integer $originatorAccount Account number of the originator
     */
    public function __construct($originatorName, $originatorBankCode, $originatorAccount, $type = 'L')
    {
        if (strlen(trim($originatorName)) == 0) {
            throw new InvalidArgumentException('Please set the name of the originator of the transfer.');
        }
        
        if (strlen(trim($originatorBankCode)) == 0) {
            throw new InvalidArgumentException('Please set the bank code of the originator of the transfer.');
        } elseif (strlen(trim($originatorBankCode)) != 8) {
            throw new InvalidArgumentException('Please check the bank code of the originator of the transfer. German bank codes are exactly eight digits long. The bank code you entered is ' . strlen(trim($originatorBankCode)) . ' digits long.');
        }
        
        if (strlen(trim($originatorAccount)) == 0) {
            throw new InvalidArgumentException('Please enter the account number of the originator of the transfer.');
        } elseif (strlen(trim($originatorAccount)) > 10) {
            throw new InvalidArgumentException('Please check the account number of the originator of the transfer. German account numbers cannot be longer than 10 digits. The account number you enteres was ' . strlen(trim($originatorAccount)) . ' digits long.');
        }
        
        if (strlen(trim($type)) == 0) {
            throw new InvalidArgumentException('Please enter the type of the transactions.');
        } elseif (strtoupper($type) != 'L' && strtoupper($type) != 'G') {
            throw new InvalidArgumentException('The two possible transaction types are \'L\' for direct debit or \'G\' for wire transfers.');
        }

        $this->_originatorName = trim($originatorName);
        $this->_originatorBankCode = trim($originatorBankCode);
        $this->_originatorAccount = trim($originatorAccount);
        
        if (strtoupper($type) == 'L') {
            $this->_type = '05';
        } else {
            $this->_type = '51';
        }
    }
    
    /**
     * Adds a transaction (C-record).
     * 
     * @return void
     * @param string $name Name of the payee or the payer
     * @param integer $bankcode Bank code of the payee or payer
     * @param integer $account Account number of the payee or payer
     * @param float $amount Amount
     * @param string $ref Reference
     * @param string $type Key (04 = debiting [Abbuchung], 05 = direct debit [Einzug], 51 = bank transfer [Überweisung], 53 = salary [Gehalt], 54 = capital-forming benefits [Vermögenswirksame Leistungen])
     * @param integer $customerId Internal customer ID
     */
    public function addTransaction($name, $bankCode, $account, $amount, $ref='', $type='05', $customerId=0)
    {
        $tmp  = 'C';
        $tmp .= '00000000';
        $tmp .= $this->_prepString($bankCode, array('length' => 8, 'fill' => FALSE));
        $tmp .= $this->_prepString($account, array('length' => 10, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        
        if ($customerId > 0) {
            $tmp .= $this->_prepString($customerId, array('length' => 13, 'fill' => TRUE, 'char' => '0', 'align' => 'left'));
        } else {
            $tmp .= '0000000000000';
        }
        
        $tmp .= $type;
        $tmp .= '000';
        $tmp .= ' ';
        $tmp .= '00000000000';
        $tmp .= $this->_originatorBankCode;
        $tmp .= $this->_prepString($this->_originatorAccount, array('length' => 10, 'fill' => TRUE, 'char' => '0', 'align' => 'left'));
        $tmp .= $this->_prepString(number_format($amount, 2, '', ''), array('length' => 11, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        $tmp .= '   ';
        
        $tmpExt = $this->_prepString($name, array('length' => 27, 'fill' => TRUE, 'char' => ' ', 'align' => 'left', 'transform' => 'upper', 'replaceUmlauts' => TRUE, 'truncate' => TRUE, 'multiline' => TRUE, 'maxLines' => 2));
        
        if (is_array($tmpExt)) {
            $this->_extensions[] = array('03', $tmpExt[1]);
            $tmp .= $tmpExt[0];
        } else {
            $tmp .= $tmpExt;
        }
        
        $tmp .= '        ';
        $tmp .= $this->_prepString($this->_originatorName, array('length' => 27, 'fill' => TRUE, 'char' => ' ', 'align' => 'left', 'transform' => 'upper', 'replaceUmlauts' => TRUE, 'mutliline' => TRUE));
        
        $tmpExt = $this->_prepString($ref, array('length' => 27, 'fill' => true, 'char' => ' ', 'align' => 'left', 'transform' => 'upper', 'replaceUmlauts' => TRUE, 'truncate' => TRUE, 'multiline' => TRUE, 'maxLines' => 13));
        
        if (is_array($tmpExt)) {
            for ($i=1, $m=count($tmpExt)-1; $i<$m; $i++) {
                $this->_extensions[] = array('02', $tmpExt[$i]);
            }
            $tmp .= $tmpExt[0];
        } else {
            $tmp .= $tmpExt;
        }
        
        $tmp .= '1';
        $tmp .= '  ';
        
        $cntExt = count($this->_extensions);
        
        $tmp .= $this->_prepString($cntExt, array('length' => 2, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        
        if ($cntExt == 0) {
            $tmp .= '                             ';
            $tmp .= '                             ';
        } elseif ($cntExt == 1) {
            $tmp .= $this->_extensions[0][0];
            $tmp .= $this->_extensions[0][1];
            $tmp .= '                             ';
        } elseif ($cntExt > 1) {
            $tmp .= $this->_extensions[0][0];
            $tmp .= $this->_extensions[0][1];
            $tmp .= $this->_extensions[1][0];
            $tmp .= $this->_extensions[1][1];
        }
        
        $tmp .= '           ';
        
        for ($i=2; $i<$cntExt; $i++) {
            $tmp .= $this->_extensions[$i][0];
            $tmp .= $this->_extensions[$i][1];
        }
        
        $recLength = 187 + $cntExt * 29;
        $tmp = $this->_prepString($recLength, array('length' => 4, 'fill' => TRUE, 'char' => '0', 'align' => 'right')) . $tmp;
        
        $this->_entries[] = $tmp;
        
        $this->_checksums['accountNumbers'] += $this->_prepString($account, array('length' => 10, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        $this->_checksums['bankCodes'] += $bankCode;
        $this->_checksums['amounts'] += $amount;
        
    }
    
    /**
     * Reads a CSV file and adds the transactions to a DTAUS file.
     * 
     * @return void
     * @throws LogicException
     * @param string $file The CSV file
     * @param array $mapping Mapping for the CSV columns. If this parameter is empty, the default mapping in $_csvDefaultMapping will be used.
     * @param string $defaultRef Default reference for transactions. Used when none is given in CSV file
     */
    public function readCsv($file, $mapping=null, $defaultRef='')
    {
        if (! file_exists($file)) {
            throw new LogicException('The file ' . $file . ' does not exist.');
        }
        
        $csvTransactions = array();
        
        if (($handle = fopen($file, 'r')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ';')) !== FALSE ) {
                $csvTransactions[] = $data;
            }
            fclose($handle);
        }
        
        if (count($csvTransactions) == 0) {
            throw new LogicException('The file ' . $file . ' appears to be empty.');
        }
        
        if (empty($mapping)) {
            $mapping = $this->_csvDefaultMapping;
        }
        
        for ($i = 0, $max = count($csvTransactions); $i < $max; $i++) {
            $tmp = $csvTransactions[$i];
            
            if (!is_array($tmp) || count($tmp) == 0) {
                next;
            }
            
            $name = $tmp[$mapping['name']];
            $bankcode = $tmp[$mapping['bankCode']];
            $account = $tmp[$mapping['account']];
            $amount = $tmp[$mapping['amount']];
            
            if (! empty($mapping['ref'])) {
                $ref = $tmp[$mapping['ref']];
            } else {
                $ref = '';
            }
            
            if (! empty($mapping['type'])) {
                $type = $tmp[$mapping['type']];
            } else {
                $type = '';
            }
            
            if (! empty($mapping['customerId'])) {
                $customerId = $tmp[$mapping['customerId']];
            } else {
                $customerId = '';
            }
            
            if (strlen($name) == 0) {
                throw new LogicException('A record in the file does have an empty name field.');
            }
            
            if (strlen($bankcode) == 0) {
                throw new LogicException('A record in the file does have an empty bank code field.');
            }
            
            if (strlen($account) == 0) {
                throw new LogicException('A record in the file does have an empty account number field.');
            }
            
            if (strlen($amount) == 0 ) {
                throw new LogicException('A record in the file does have an empty amount field.');
            }
            
            if (strlen($ref) == 0) {
                if (strlen($defaultRef) == 0) {
                    throw new LogicException('A record in the file does have an empty reference field and no global reference has been set.');
                } else {
                    $ref = $defaultRef;
                }
            }
            
            if (strlen($type) == 0) {
                $type = $this->_type;
            }
            
            if (strlen($customerId) == 0) {
                $customerId = 0;
            }
            
            $this->addTransaction($name, $bankcode, $account, $amount, $ref, $type, $customerId);
        }
    }
    
    /**
     * Creates a DTAUS file and returns it
     * 
     * @return string
     * @throws LogicException
     * @throws InvalidArgumentException
     * @param string $type Type. Either L for debit (Lastschrift) or G for credit note (Gutschrift)
     * @param string $ref Collective reference number of the client
     * @param string $execDate Alternative execution date (ddmmyyyy)
     */
    public function createDtaus($type='L', $ref=0, $execDate='')
    {
        if (count($this->_entries) == 0) {
            throw new LogicException('No transactions found.');
        }
        
        $tmp = $this->_header($type, $ref, $execDate);
        
        for ($i = 0, $max = count($this->_entries); $i < $max; $i++) {
            $tmp .= $this->_entries[$i];
        }
        
        $tmp .= $this->_footer();
        return $tmp;
    }
    
    /**
     * Transform a string into the desired format. The options array may contain none or several
     * of the following elements:
     *     + length (integer) - the max. length of the converted string (defaults to '10').
     *     + fill (boolean) - whether or not to fill the string to the length specified in 'length'
     *       (default TRUE).
     *     + char (string) - character to be used to fill the string (default ' ' [space]).
     *     + align (string) - whether to fill the string to the left with the character specified
     *       in 'char' or to the right (default 'left'). Possible values are 'left' and
     *       'right'.
     *     + transform (string) - whether to transform the result string to upper case ('upper'),
     *       to lower case ('lower'), or not at all ('none'). Default is 'none'.
     *     + replaceUmlauts (boolean) - whether or not to transliterate German umlauts. Default is
     *       FALSE.
     *     + truncate (boolean) - whether or not to truncate the result string if it is longer then
     *       'length'. Default is FALSE.
     *     + multiline (boolean) - determines if multiple lines should be created if necessary.
     *       Default is FALSE.
     *     + maxLines (integer) - max. number of lines to be created if 'multiline' is set to TRUE.
     *       Default is '0'. Setting 'maxlines' to zero and 'multiline' to TRUE will result in as
     *       many lines as are required to split the whole input string.
     * 
     * @return string
     * @throws InvalidArgumentException
     * @param string $str The source string
     * @param array options Array of options described above
     */
    protected function _prepString($str, $options = array())
    {
        $str = trim($str);
        
        // Check input string
        if (strlen($str) == 0) {
            throw new InvalidArgumentException('String $str cannot be empty.');
        }
        
        // Set options from $options array
        $len = (!array_key_exists('length', $options) || strlen(trim($options['length'])) == 0) ? 10 : $options['length'];
        $fill = (!array_key_exists('fill', $options) || !is_bool($options['fill'])) ? TRUE : $options['fill'];
        $char = (!array_key_exists('char', $options) || strlen(trim($options['char'])) == 0) ? ' ' : $options['char'];
        $align = (!array_key_exists('align', $options) || strlen(trim($options['align'])) == 0) ? 'left' : $options['align'];
        $transform = (!array_key_exists('transform', $options) || strlen(trim($options['transform'])) == 0) ? 'none' : $options['transform'];
        $replaceUmlauts = (!array_key_exists('replaceUmlauts', $options) || !is_bool($options['replaceUmlauts'])) ? FALSE : $options['replaceUmlauts'];
        $truncate = (!array_key_exists('truncate', $options) || !is_bool($options['truncate'])) ? FALSE : $options['truncate'];
        $multiline = (!array_key_exists('multiline', $options) || !is_bool($options['multiline'])) ? FALSE : $options['multiline'];
        $maxLines = (!array_key_exists('maxLines', $options) || strlen(trim($options['maxLines'])) == 0) ? 0 : $options['maxLines'];
        
        // Check options
        if (intval($len).'' != $len) {
            throw new InvalidArgumentException('Option \'length\' must be an integer value. [' . intval($len).'' . '] | [' . $len . ']');
        }
        
        if ($fill !== TRUE && $fill !== FALSE) {
            throw new InvalidArgumentException('Option \'fill\' must be of type Boolean.');
        }
        
        if (strlen(trim($char)) > 1) {
            throw new InvalidArgumentException('Option \'char\' cannot exceed one character.');
        }
        
        if ($align != 'left' && $align != 'right') {
            throw new InvalidArgumentException('Option \'align\' must be either \'left\' or \'right\'.');
        }
        
        if ($transform != 'upper' && $transform != 'lower' && $transform != 'none') {
            throw new InvalidArgumentException('Option \'transform\' must be \'upper\', \'lower\', or \'none\'.');
        }
        
        if ($replaceUmlauts !== TRUE && $replaceUmlauts !== FALSE) {
            throw new InvalidArgumentException('Option \'replaceUmlauts\' must be of type Boolean.');
        }
        
        if ($truncate !== TRUE && $truncate !== FALSE) {
            throw new InvalidArgumentException('Option \'truncate\' must be of type Boolean.');
        }
        
        if ($multiline !== TRUE && $multiline !== FALSE) {
            throw new InvalidArgumentException('Option \'mutliline\' must be of type Boolean.');
        }
        
        if (intval($maxLines).'' != $maxLines) {
            throw new InvalidArgumentException('Option \'maxLines\' must be of type Integer.');
        }
        
        // Process string
        
        // Transliterate German umlauts if required and the German 'ß'. This has to be done first,
        // since it changes the total length of the string.
        if ($replaceUmlauts) {
            
            // Umlauts are transliterated by appending an 'e' to the base vowel.
            $str = str_replace( 'ä', 'ae', $str );
            $str = str_replace( 'ö', 'oe', $str );
            $str = str_replace( 'ü', 'ue', $str );
            $str = str_replace( 'Ä', 'Ae', $str );
            $str = str_replace( 'Ö', 'Oe', $str );
            $str = str_replace( 'Ü', 'Ue', $str );
            
        }
        
        // The German 'ß' is always replaced by 'ss'.
        $str = str_replace('ß', 'ss', $str);
        
        // Next we transform the string to upper or lower case, if requested
        if ($transform == 'upper') {
            $str = strtoupper($str);
        } elseif($transform == 'lower') {
            $str = strtolower($str);
        }
        
        // Truncate the string if requested (mind multiline and maxLines settings)
        if ($truncate && (strlen($str) > $len) && $multiline) {
            
            $returnArray = str_split($str, $len);
            
            if ($maxLines > 0) {
                $returnArray = array_slice($returnArray, 0, $maxLines);
            }
            
        } elseif ($truncate && (strlen($str) > $len) && !$multiline ) {
            $returnArray = array(substr($str, 0, $len));
        } elseif (! $truncate && (strlen($str) > $len)) {
            throw new LengthException('String ' . $str . ' is too long and truncating it has been pohibited.');
            //echo $str . ' LENGTH EXCEPTION<hr />';
        } else {
            $returnArray = array($str);
        }
        
        // Fill the string either to the left or to the right as requested.
        if ($fill && strlen($char) > 0 && ($align == 'left' || $align == 'right')) {
            
            for ($i = 0, $m = count($returnArray); $i < $m; $i++ ) {
                
                while (strlen($returnArray[$i]) < $len) {
                    
                    if ($align == 'left') {
                        $returnArray[$i] = $returnArray[$i] . $char;
                    } elseif ($align == 'right') {
                        $returnArray[$i] = $char . $returnArray[$i];
                    }
                    
                }
                
            }
            
        }
        
        if (count($returnArray) == 1) {
            return $returnArray[0];
        } else {
            return $returnArray;
        }
    }
    
    /**
     * Creates an E-record (footer). This contains the number of C-records and the checksums for
     * account numbers, bank codes and amounts.
     * 
     * @return string
     */
    protected function _footer()
    {
        $tmp  = '0128';
        $tmp .= 'E';
        $tmp .= '     ';
        $tmp .= $this->_prepString(count($this->_entries), array('length' => 7, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        $tmp .= '0000000000000';
        $tmp .= $this->_prepString($this->_checksums['accountNumbers'], array('length' => 17, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        $tmp .= $this->_prepString($this->_checksums['bankCodes'], array('length' => 17, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        $tmp .= $this->_prepString(number_format($this->_checksums['amounts'], 2, '', ''), array('length' => 13, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        $tmp .= '                                                   ';
        
        return $tmp;
    }
    
    /**
     * Creates the A-record (header).
     * 
     * @return string
     * @throws InvalidArgumentException
     * @param string $type $type Type. Either L for debit (Lastschrift) or G for credit note (Gutschrift)
     * @param string $ref Collective reference number of the client
     * @param string $execDate Alternative execution date (ddmmyyyy)
     */
    protected function _header($type, $ref=0, $execDate='')
    {
        if ($type != 'L' && $type != 'G') {
            throw new InvalidArgumentException('Invalid type. Type must be either \'L\' for debits or \'G\' for credit notes.');
        }
        
        if (strlen($ref) > 10) {
            throw new InvalidArgumentException('The collective reference number must not be longer than 10 digits.');
        }
        
        if (strlen($execDate) > 0 && strlen($execDate) != 8) {
            throw new InvalidArgumentException('Please enter the execution date in the format \'ddmmyyyy\'.');
        } elseif (strlen($execDate) == 8) {
            $tmpDay = substr($execDate, 0, 2);
            $tmpMonth = substr($execDate, 2, 2);
            $tmpYear = substr($execDate, 4, 4);
            
            $nowTs = mktime(12, 0, 0);
            $thenTs = mktime(23, 59, 59, $tmpMonth, $tmpDay, $tmpYear);
            $fifteenDays = 60 * 60 * 24 * 15;
            
            if ($thenTs > ($nowTs + $fifteenDays)) {
                throw new LogicException('The alternative execution date can be no more than fifteen days in the future.');
            }
        }
        
        $tmp  = '';
        $tmp .= '0128';
        $tmp .= 'A';
        $tmp .= $type . 'K';
        $tmp .= $this->_originatorBankCode;
        $tmp .= '00000000';
        
        $tmpExt = $this->_prepString($this->_originatorName, array(
            'length' => 27,
            'fill' => TRUE,
            'char' => ' ',
            'align' => 'left',
            'transform' => 'upper',
            'replaceUmlauts' => TRUE,
            'truncate' => TRUE,
            'mutliline' => TRUE,
            'maxLines' => 2
        ));
        
        if (is_array($tmpExt)) {
            $this->_extensions[] = array('01', $tmpExt[1]);
            $tmp .= $tmpExt[0];
        } else {
            $tmp .= $tmpExt;
        }
        
        $tmp .= date('dmy');
        $tmp .= '    ';
        $tmp .= $this->_prepString($this->_originatorAccount, array('length' => 10, 'fill' => TRUE, 'char' => '0', 'align' => 'right'));
        $tmp .= $this->_prepString($ref, array('length' => 10, 'fill' => TRUE, 'char' => '0'));
        $tmp .= '               ';
        
        if (strlen($execDate) == 8) {
            $tmp .= $execDate;
        } else {
            $tmp .= '        ';
        }
        
        $tmp .= '                        ';
        $tmp .= '1';
        
        return $tmp;
    }
}
?>