<?php
/** Typowe operacje na stringach jako metody statyczne.
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage test
 */
class Common_String {
    /**
     * Zamienia wszystkie znaki specjalne (za wyjątkiem a-z, A-Z oraz 0-9) na "_".
     *
     * @param string $sInput
     * @return string
     */
    public static function replaceSpecialChars($sInput) {
        $old_pattern = array("/[^a-zA-Z0-9]/", "/_+/", "/_$/");
        $new_pattern = array("_", "_", "_");
        return preg_replace($old_pattern, $new_pattern, $sInput);
    }

    static public function i2d( &$aRow, array $aFields, $sFormat = "Y-m-d" ) {
        if (is_array($aRow)) {
            self::i2dForArray( $aRow, $aFields, $sFormat );
        }
        if (is_object($aRow)) {
            self::i2dForObject( $aRow, $aFields, $sFormat );
        }
    }

    static public function i2dForArray( array &$aRow, array $aFields, $sFormat = "Y-m-d" ) {
        foreach($aFields as $sField) {
            if ( isset($aRow[$sField]) ) {
                if (!empty($aRow[$sField])) {
                    $aRow[$sField] = date( $sFormat, $aRow[$sField]);
                }
            }
        }
    }

    static public function i2dForObject( &$oRow, array $aFields, $sFormat = "Y-m-d" ) {
        foreach($aFields as $sField) {
            if (!empty($oRow->{$sField})) {
                $oRow->{$sField} = date( $sFormat, $oRow->{$sField});
            }
        }
    }

    static public function d2i( array &$aRow, array $aFields ) {
        foreach($aFields as $sField) {
            if ( isset($aRow[$sField]) ) {
                $aRow[$sField] = strtotime( $aRow[$sField] );
            }
        }
    }

	public static function convertToLinkName($sInput) {
		$sResult = $sInput;
		$aPolishChars = array('ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n',
			'ó' => 'o', 'ś' => 's', 'ź' => 'z', 'ż' => 'z');
		foreach ($aPolishChars as $sChar => $sReplacement) {
			$sResult = str_ireplace($sChar, $sReplacement, $sResult);
		}
		$sResult = strtolower($sResult);
		$sResult = self::replaceSpecialChars($sResult);
		return str_replace(' ', '_', $sResult);
	}

}