<?php
/*
 * @author Adam Nielski
 * @copyright Web-Soft.com.pl
 * @version 1.0
 * @package common
 * @subpackage form
 */
class Common_Form_TranslatePL {
    private $_polish;
    public static function getPolishTranslation() {
        $_polish = array("isEmpty" => "Pole nie może być puste",
            "stringLengthTooShort" => "'%value%' zawiera mniej niż %min% znaków",
            "stringLengthTooLong" => "'%value%' zawiera więcej niż %max% znaków",
            "notBetween" => "'%value%' nie zawiera się pomiędzy '%min%' a '%max%'",
            "notBetweenStrict" => "'%value%' nie zawiera się pomiędzy '%min%' a '%max%'",
            "emailAddressInvalid" => "'%value%' nie jest poprawnym adresem e-mail",
            "stringEmpty" => "'%value%' jest pustą wartością",
            "notAlnum" => "'%value%' zawiera niedozwolone znaki. Dozwolone są tylko liczby i cyfry.",
            "invalidLength" => "'%value%' powinien zawierac XX znaków",
            "regexNotMatch" => "'%value%' nie pasuje do wzorca '%pattern%'",
            Zend_Validate_NotEmpty::INVALID => "Proszę wypełnić pola",
            "fileNotExistsDoesExist" => "Brak pliku",
            "fileUploadErrorNoFile" => "Brak pliku",
            "fileExtensionFalse" => "Tylko pliki .jpg, .gif, .png",
            "fileFilesSizeTooBig" => "Max rozmiar pliku to 40kb",
            "fileImageSizeWidthTooBig" => "Max szerokość to '%maxwidth%'",
            "fileImageSizeWidthTooSmall" => "Minimalna szerokość to '%minwidth%'",
            "fileImageSizeHeightTooBig" => "Max wysokość to '%maxheight%'",
            "fileImageSizeHeightTooSmall" => "Minimalna wysokość to '%minheight%'",
            "notInt" => "'%value%' nie jest liczbą",
            "notGreaterThan" => "'%value%' nie jest większe od  '%min%'",
            'notGreaterThanOrEq' => "'%value%' nie jest większy lub równy od '%min%'",
            'notFloat' => "'%value%' nie jest liczbą zmiennoprzecinkową",
            'isEmpty' => "Wartość jest wymagana i nie może być pusta",
            'stringLengthInvalid' => 'Zły typ danych, powinien być typu string.',
            'emailAddressInvalidFormat' => "'%value%' nie jest poprawnym adresem email prostego formatu local-part@hostname"
        );
        return $_polish;
    }
}
