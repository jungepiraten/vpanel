<?php // coding: utf-8
/**
 * Library fuer Datenaustausch EBICS
 * @package library.ebics
 * @author Hans-Stefan Mueller
 * @copyright Copyright (C) 2006-2011 Hans-Stefan Mueller
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or later
 * @version $Id: sepa.php 2306 2014-02-14 15:19:56Z root $
 * @link http://www.m-internet.de www.m-internet.de
*/

// no direct access
//defined( '_JEXEC' ) or die( 'Restricted access' );


/**
 * Library Klasse
 * EBICS - Electronic Banking Internet Communication Standard - http://www.ebics.de
 * SEPA-Datenformate nach SIO 20022 - Spezifikation der Deutschen Kreditwirtschaft
 * Version 2.7 vom 25.3.2013 (gueltig ab 4.11.2013)
 * @package library.ebics
 */
class EBICS_Sepa {

	/* Timestamp (Linux-Date) */
	var $iTimeStamp;
	/* Eindeutige ID der SEPA-Nachricht */
	var $sMessageId;
	/* Payment Information <PmtInf> */
	var $oPaymentInformation;
	/* Transaction Information <DrctDbtTxInf> */
	var $aTransactionInformation;
	/* LocalInstrument <LclInstrm>  Werte: CORE, COR1, B2B */
	var $PaymentLclInstrm;
	/* Summe aller Transaktionen */
	var $TransactionCtrlSum;
	/* Zeichensatz fuer SEPA-Nachrichten */
	var $validChars = array(
		0x20, 0x27, 0x28, 0x29, 0x2b, 0x2c, 0x2d, 0x2e, 0x2f, 0x3a, 0x3f,		// Sonderzeichen
		0x30, 0x31, 0x32, 0x33, 0x34, 0x35, 0x36, 0x37, 0x38, 0x39,				// Ziffern
		0x41, 0x42, 0x43, 0x44, 0x45, 0x46, 0x47, 0x48, 0x49, 0x4a, 0x4b, 0x4c, 0x4d, 0x4e, 0x4f, 
		0x50, 0x51, 0x52, 0x53, 0x54, 0x55, 0x56, 0x57, 0x58, 0x59, 0x5a,	// Grossbuchstaben
		0x61, 0x62, 0x63, 0x64, 0x65, 0x66, 0x67, 0x68, 0x69, 0x6a, 0x6b, 0x6c, 0x6d, 0x6e, 0x6f, 
		0x70, 0x71, 0x72, 0x73, 0x74, 0x75, 0x76, 0x77, 0x78, 0x79, 0x7a);	// Kleinbuchstaben
	/* Fehlerliste */
	var $errors = array();

	/**
	 * Konstruktor-Funktion
	 * ....
	 */
	function EBICS_Sepa( $InitiatorName ) {
        $this->iTimeStamp = time();
		$this->sMessageId = 'ID-' . $this->iTimeStamp;	// Eindeutige ID
		$this->sInitiatorName = $this->testString( $InitiatorName, 70 );
        $this->oPaymentInformation = new stdClass();
		$this->aTransactionInformation = array();
		$this->TransactionCtrlSum = 0.0;
		
    }


	/**
	 * Ausfuehrungsdatum setzen
	 * fuer CORE: erst- und einmalige LS 5 Tage vorher, wiederkehrende und letzmalige 2 Tage vorher (Mo-Fr), bei Einreichung bis 7.00 Uhr
	 * @param int Linux-Timestamp
	 * @return void
	 */
	function setReqdColltnDt( $time=null ) {
		$max_time = strtotime( "+8 day" );
		if ( !empty( $time ) && $time > $this->timestamp && $time <= $max_time ) {
			$this->execution_date = date( 'dmY', $time );
		}
	}


    /**
	 * Teste Zeichenkette auf gueltige Zeichen und Umwandlung von Sonderzeichen
	 * @param  string  $subject Zeichenkette fuer den Test
	 * @return string
	 */
    function testString ( $sString, $nMax=NULL ) {
		$search  = array( 'Ä', 'Ö', 'Ü', 'ß', 'ä', 'ö', 'ü', '&', '*', '$', '%' );
		$replace = array( 'Ae', 'Oe', 'Ue', 'ss', 'ae', 'oe', 'ue', '+', '.', '.', '.' ); 
		$sResult = str_replace( $search, $replace, $sString );				// Umlaute/Sonderzeichen Konvertieren
		if ( !is_null( $nMax )) $sResult = substr( $sResult, 0, $nMax );	// Max. Laenge
		for ( $i = 0; $i < strlen($sResult); $i++ ) {									// Unzulaessige Zeichen ersetzten
			if (! in_array( ord( substr( $sResult, $i, 1)), $this->validChars )) {
				$sResult[$i] = " ";
			}
		}
		return $sResult;
    }

	/**
	 * setPaymentInformation -- Informationen zum Glaeubiger und zur Typ der Zahlung
	 * @param array $aCdtr Kontodaten des Glaeubigers (Kreditors)
	 * @param string $ReqdColltnDt Faelligkeitsdatum JJJJ-MM-TT
	 * @param string $SeqTp Wiederholung FRST=Erst LS|RCUR=Folge LS|OOFF=Einmal LS|FNAL=letze LS
	 * @param string $LclInstrm Art der Lastschrift CORE=Basis LS | COR1=SEPA-Basislastschrift mit D-1-Vereinbarung | B2B=Firmen-LS
	 * @return boolean
	 */
    function setPaymentInformation( $aCdtr, $ReqdColltnDt=NULL, $SeqTp='OOFF', $LclInstrm='CORE' ) {
		$bError = false;
		$this->oPaymentInformation->LclInstrm = $LclInstrm;
		$this->oPaymentInformation->SeqTp = $SeqTp;
//		$this->oPaymentInformation->CtgyPurp = '';
		$this->oPaymentInformation->ReqdColltnDt = $ReqdColltnDt;
		$this->oPaymentInformation->CdtrNm = $this->testString( $aCdtr['name'], 70);
		$this->oPaymentInformation->CdtrAcct = $aCdtr['iban'];
		$this->oPaymentInformation->CdtrAgt = $aCdtr['bic'];
		$this->oPaymentInformation->CdtrSchmeId = $aCdtr['glaeubiger_id'];
		
		if ( !strlen( $this->oPaymentInformation->CdtrSchmeId ) > 7 ) { $this->errors[] = '(PI) Gläubiger-Identifikationsnummer (CI) hat zu wenig Zeichen: ' . $this->oPaymentInformation->CdtrSchmeId; $bError = true; }
		if ( !strlen( $this->oPaymentInformation->CdtrNm ) > 0 ) { $this->errors[] = '(PI) Name des Gläubigers (Kreditors) ist leer'; $bError = true; }
        if ( strlen( $this->oPaymentInformation->CdtrAgt ) < 8 && strlen($this->oPaymentInformation->CdtrAgt) > 11 ) { $this->errors[] = '(PI) BIC des Gläubigers (Kreditors) hat eine unzulässige Länge: ' . $this->oPaymentInformation->CdtrAgt; $bError = true; }
		if ( strlen( $this->oPaymentInformation->CdtrAcct ) != 22 ) { $this->errors[] = '(PI) IBAN des Gläubigers (Kreditors) muss 22-stellig sein: '.  $this->oPaymentInformation->CdtrAcct; $bError = true; }
		// Pruefung IBAN Pruefziffer ???
        if ( !is_numeric( substr( $this->oPaymentInformation->CdtrAcct, 2 ))) { $this->errors[] = '(PI) IBAN des Gläubigers (Kreditors) ist ab der 3. Stelle keine Zahl: '. $this->oPaymentInformation->CdtrAcct; $bError = true;}
        return $bError;
    }

	/**
	 * addTransaction -- Transaktion (Zahlungs- und Schuldnerdaten) hinzufuegen
	 * @param array $aDbtr Kontodaten des Schuldners (Debitor)
	 * @param string $aMndt Daten des LS-Mandats
	 * @param string $dInstdAmt Zahlbetrag
	 * @param string $sRmtInf Verwendungszweck (<=140 Zeichen) bzw. 4 x 35 Zeichen VR-Networld
	 * @return boolean
	 */
    function addTransaction($aDbtr, $aMndt, $dInstdAmt, $sRmtInf ) {
		$bError = false;
		$oTransaction = new stdClass();
		$oTransaction->InstdAmt = $dInstdAmt;		// Zahlbetrag
		$oTransaction->MndtId = $aMndt['id'];			// Mandats-ID
		$oTransaction->DtOfSgntr = $aMndt['datum'];	// Ausstellungsdatum des Mandats
		$oTransaction->OrgnlMndtId = isset( $aMndt['id_alt'] ) ? $aMndt['id_alt'] : NULL;	// Alte Mandats-ID
		$oTransaction->DbtrAgt = $aDbtr['bic'];		// BIC des Schuldners (Debitors)
		$oTransaction->DbtrNm = $this->testString( $aDbtr['name'], 70 );		// Name des Schuldners (Debitors)
		$oTransaction->DbtrAcct = $aDbtr['iban'];		// IBAN des Schuldners (Debitors)
//		$oTransaction->Purp = '';
		$oTransaction->RmtInf = $this->testString( $sRmtInf, 140 );		// Verwendungszweck max. 140 Zeichen

		if ( empty( $oTransaction->MndtId )) { $this->errors[] = '(TI) Mandats-ID des Schuldners (Debitors) ist leer'; $bError = true; }
		if ( empty( $oTransaction->InstdAmt) || $oTransaction->InstdAmt < 0 ) { $this->errors[] = '(TI) Der Zahlbetrag hat einen unzulaessigen Wert: ' . number_format( $oTransaction->InstdAmt, 2, '.', '' ) . ' EUR'; $bError = true; }
        if ( strlen( $oTransaction->DbtrAgt ) < 8 && strlen( $oTransaction->DbtrAgt ) > 11 ) { $this->errors[] = '(TI) BIC des Schuldners (Debitors) hat eine unzulässige Länge: ' . $oTransaction->DbtrAgt; $bError = true; }
		if ( !strlen( $oTransaction->DbtrNm ) > 0 ) { $this->errors[] = '(TI) Name des Schuldners (Debitors) ist leer'; $bError = true; }
		if ( strlen( $oTransaction->DbtrAcct ) != 22 ) { $this->errors[] = '(TI) IBAN des Schuldners (Debitors) muss 22-stellig sein: '.  $oTransaction->DbtrAcct; $bError = true; }
		// Pruefung IBAN Pruefziffer ???
        if ( !is_numeric( substr( $oTransaction->DbtrAcct, 2 ))) { $this->errors[] = '(TI) IBAN des Schuldners (Debitors) ist ab der 3. Stelle keine Zahl: '. $oTransaction->DbtrAcct; $bError = true;}

        if ( !$bError ) {
			$this->aTransactionInformation[] = $oTransaction;
			$this->TransactionCtrlSum += $oTransaction->InstdAmt;
		}
        return $bError;
    }


	/**
	 * getDirectDebitInitiation - SEPA-Lastschrifteinzugsauftrag / SEPA Direct Debit (SDD - pain.008.003.02)
	 * @return string XML-Nachricht (UTF-8)
	 */
    function getDirectDebitInitiation() {
		// ISO 20022 MXL-Nachricht fuer SEPA-Lastschrifteinzugsauftrag pain.008.003.02 - Direct Debit Initiation [CstmrDrctDbtInitn]
		$content = '<?xml version="1.0" encoding="UTF-8"?>';
		
		// Version 2.7 (gueltig ab 4.11.2013)
		$content .= '<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.003.02" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.008.003.02 pain.008.003.02.xsd">';
		
		$content .= '<CstmrDrctDbtInitn>';	// Kunden-SEPA-Lastschrifteinzugsauftrag
		// GroupHeaderSDD
		$content .= '<GrpHdr>';	// Kenndaten, die fuer alle Transaktionen innerhalb der SEPA-Nachricht gelten
		// MessageIdentification (eindeutiger Name)
		$content .= '<MsgId>' . $this->sMessageId . '</MsgId>';
		$content .= '<CreDtTm>' . date( 'Y-m-d\TH:i:s' ) . '.000Z</CreDtTm>';   // 2010-11-21T09:30:47.000Z
		$content .= '<NbOfTxs>' . count( $this->aTransactionInformation ) . '</NbOfTxs>';
		$content .= '<CtrlSum>' . number_format( $this->TransactionCtrlSum, 2, '.', '' ) . '</CtrlSum>';
		$content .= '<InitgPty><Nm>' . $this->sInitiatorName . '</Nm></InitgPty>';
		$content .= '</GrpHdr>';
		
		// PaymentInstructionInformationSDD
		
		// Payment Information -> Satz von Informationen fuer alle Transaktionen
		$content .= '<PmtInf>';
		$content .= '<PmtInfId>' . $this->sMessageId . '-PI001</PmtInfId>';	// Eindeutige Referenz des folgenden Sammlers
		$content .= '<PmtMtd>DD</PmtMtd>';		// PaymentMethod (default: DD)
		$content .= '<BtchBookg>true</BtchBookg>';		// Wird verwendet um Sammler zu verhindern (default: true = Sammelbuchung)		
		$content .= '<NbOfTxs>' . count( $this->aTransactionInformation ) . '</NbOfTxs>';		// Anzahl der Transaktionen innerhalb eines PaymentInformation-Blocks
		$content .= '<CtrlSum>' . number_format( $this->TransactionCtrlSum, 2, '.', '' ) . '</CtrlSum>';
		$content .= '<PmtTpInf>';
		$content .= '<SvcLvl><Cd>SEPA</Cd></SvcLvl>';		// ServiceLevel (default: SEPA)
		$content .= '<LclInstrm><Cd>' . $this->oPaymentInformation->LclInstrm . '</Cd></LclInstrm>';		// LocalInstrument ( default: CORE)
		$content .= '<SeqTp>' . $this->oPaymentInformation->SeqTp . '</SeqTp>';	// SequenceType - Art der Wiederholung
//		$content .= '<CtgyPurp>' . $this->oPaymentInformation->CtgyPurp . '</CtgyPurp>';	// CategoryPurpose: 4-stellige Codes fuer Verwendungsschluessel (ISO 20022) -- optional nicht implementiert
		$content .= '</PmtTpInf>';
		$content .= '<ReqdColltnDt>' . $this->oPaymentInformation->ReqdColltnDt . '</ReqdColltnDt>';		// RequestedCollectionDate - Faelligkeitsdatum der LS
		$content .= '<Cdtr><Nm>' . $this->oPaymentInformation->CdtrNm . '</Nm></Cdtr>';		// Name Glaeubiger
		$content .= '<CdtrAcct> <Id> <IBAN>' . $this->oPaymentInformation->CdtrAcct . '</IBAN> </Id> </CdtrAcct>';		// IBAN Glaeubiger
		$content .= '<CdtrAgt> <FinInstnId> <BIC>' . $this->oPaymentInformation->CdtrAgt . '</BIC> </FinInstnId> </CdtrAgt>';		// BIC Glaeubiger
//		$content .= '<UltmtCdtr>...</UltmtCdtr>';		// abweichender Zahlungsempfaenger --> nicht verwendet
//		$content .= '<ChrgBr>SLEV</ChrgBr>';		// ChargeBearer - Entgeltverrechnung optional (default: SLEV)
		$content .= '<CdtrSchmeId><Id><PrvtId><Othr><Id>' . $this->oPaymentInformation->CdtrSchmeId . '</Id><SchmeNm><Prtry>SEPA</Prtry></SchmeNm></Othr></PrvtId></Id></CdtrSchmeId>';

		// Direct Debit Transaction Information
		for ($i = 0; $i <  count( $this->aTransactionInformation ); $i++ ) {
			// Einzeltransaktion mit Information zum Schuldner (Debitor)
			$content .= '<DrctDbtTxInf>';	// Einzeltransaktion	
			$content .= '<PmtId><EndToEndId>' . $this->sMessageId . '-PI001-TI' . sprintf( '%03d', $i+1 ) . '</EndToEndId></PmtId>';
			$content .= '<InstdAmt Ccy="EUR">' . number_format( $this->aTransactionInformation[$i]->InstdAmt, 2, '.', '' ) . '</InstdAmt>';
//			$content .= '<ChrgBr>SLEV</ChrgBr>';		// ChargeBearer - Entgeltverrechnung --> nicht hier, sondern bei PaymentInformation verwenden!!
			
			// Direct Debit Transaction (DrctDbtTx) = Informationen zm Lastschrift-Mandat
			$content .= '<DrctDbtTx>';
			// Mandatsbezogene Informationen = MandateRelatedInformation <MndtRltdInf>
			$content .= '<MndtRltdInf>';
			$content .= '<MndtId>' . $this->aTransactionInformation[$i]->MndtId .'</MndtId>';				// ID des Mandats
			$content .= '<DtOfSgntr>' . $this->aTransactionInformation[$i]->DtOfSgntr .'</DtOfSgntr>';		// Ausstellungsdatum des Mandats YYYY-MM-DD
			// Aenderungen im Mandat --> Angaben zum bisherigen Mandat
			if ( ! is_null( $this->aTransactionInformation[$i]->OrgnlMndtId )) {
				$content .= '<AmdmntInd>true</AmdmntInd>';			// Veraendertes Mandat
				// Details der Mandatsaenderung = Amendment Information Details <AmdmntInfDtls>
				$content .= '<AmdmntInfDtls>';
				$content .= '<OrgnlMndtId>' . $this->aTransactionInformation[$i]->OrgnlMndtId .'<OrgnlMndtId>';		// ID des bisherigen Mandats bei veraendertem Mandat
/*			
			// Informationen zum bisherigen Glaeubiger
			$content .= '<OrgnlCdtrSchmeId>';		// Aenderungen bei Glaeubiger (Kreditor) --> bisherige Glaeubigerdaten
			$content .= '<Nm>...Name...</Nm>';		// urspruenglicher Name des Glaeubigers (Kreditors)
			$content .= '<Id><PrvtId><Othr><Id>...Glaeubiger-ID...</Id><SchmeNm><Prtry>SEPA</Prtry></SchmeNm></Othr></PrvtId></Id>';
			$content .= '</OrgnlCdtrSchmeId>';
			// Informationen zur bisherigen Kontoverbindung
			$content .= '<OrgnlDbtrAcct><Id><IBAN>...IBAN...</IBAN></Id></DbtrAcct>';			// Bisherige Kontonummer des Schuldners
			$content .= '<OrgnlDbtrAgt><FinInstnId><Other><Id>...</Id></Other></FinInstnId></DbtrAgt>';		// bisherige Bank des Schuldners
*/
				$content .= '</AmdmntInfDtls>';
			}
//			$content .= '<ElctrncSgntr>...<ElctrncSgntr>';		// nur fuer elektronische Signatur --> nicht implementiert
			$content .= '</MndtRltdInf>';
//			$content .= '<CdtrSchemeId>...</CdtrSchemeId>';		// Glaeubiger-ID entfaellt, wenn diese bei PaymentInformationen angegeben wurde!
			$content .= '</DrctDbtTx>';
			
//			$content .= '<UltmtCdtr>...</UltmtCdtr>';		// abweichender Zahlungsempfaenger --> nicht verwendet
			$content .= '<DbtrAgt><FinInstnId><BIC>' . $this->aTransactionInformation[$i]->DbtrAgt . '</BIC></FinInstnId></DbtrAgt>';		// BIC Schuldner	
			$content .= '<Dbtr><Nm>' . $this->aTransactionInformation[$i]->DbtrNm . '</Nm></Dbtr>';	// Name des Schuldners max. 70 Zeichen		
			$content .= '<DbtrAcct><Id><IBAN>' . $this->aTransactionInformation[$i]->DbtrAcct . '</IBAN></Id></DbtrAcct>';		
//			$content .= '<UltmtDbtr><Nm>...</Nm></UltmtDbtr>';		// Abweichender Schuldner (Debitor) --> nicht verwendet
//			$content .= '<Purp>' . $this->aTransactionInformation[$i]->Purp . '</Purp>';	// Purpose: 11-stellige Codes fuer Verwendungsschluessel (ISO 20022) -- optional nicht implementiert
			$content .= '<RmtInf><Ustrd>' . $this->aTransactionInformation[$i]->RmtInf . '</Ustrd></RmtInf>';		// Remittance Information <RmtInf> = Verwendungszweckinformation 140 Zeichen
			$content .= '</DrctDbtTxInf>';		
		}
		$content .= '</PmtInf>';		

		// XML - Dokument Ende
		
		$content .= '</CstmrDrctDbtInitn>';
		$content .= '</Document>';

        return $content;
    }


	/**
	 * Writes the EBICS file.
	 * @param  string  $filename Filename.
	 * @return boolean
	 */
    function saveFile( $filename ) {
        $content = $this->getDirectDebitInitiation();

        $fpEbics = @fopen($filename, "w");
        if (!$fpEbics) {
            $result = false;
        } else {
            $result = @fwrite($fpEbics, $content);
            @fclose($fpEbics);
        }

        return $result;
    }
	
// Ende der Klasse
}

