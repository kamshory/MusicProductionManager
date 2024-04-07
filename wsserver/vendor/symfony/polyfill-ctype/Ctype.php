<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Ctype;

/**
 * Ctype implementation through regex.
 *
 * @internal
 *
 * @author Gert de Pagter <BackEndTea@gmail.com>
 */
final class Ctype
{
    /**
     * Returns TRUE if every character in text is either a letter or a digit, FALSE otherwise.
     *
     * @see https://php.net/ctype-alnum
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_alnum($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^A-Za-z0-9]/', $text);
    }

    /**
     * Returns TRUE if every character in text is a letter, FALSE otherwise.
     *
     * @see https://php.net/ctype-alpha
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_alpha($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^A-Za-z]/', $text);
    }

    /**
     * Returns TRUE if every character in text is a control character from the current locale, FALSE otherwise.
     *
     * @see https://php.net/ctype-cntrl
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_cntrl($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^\x00-\x1f\x7f]/', $text);
    }

    /**
     * Returns TRUE if every character in the string text is a decimal digit, FALSE otherwise.
     *
     * @see https://php.net/ctype-digit
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_digit($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^0-9]/', $text);
    }

    /**
     * Returns TRUE if every character in text is printable and actually creates visible output (no white space), FALSE otherwise.
     *
     * @see https://php.net/ctype-graph
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_graph($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^!-~]/', $text);
    }

    /**
     * Returns TRUE if every character in text is a lowercase letter.
     *
     * @see https://php.net/ctype-lower
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_lower($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^a-z]/', $text);
    }

    /**
     * Returns TRUE if every character in text will actually create output (including blanks). Returns FALSE if text contains control characters or characters that do not have any output or control function at all.
     *
     * @see https://php.net/ctype-print
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_print($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^ -~]/', $text);
    }

    /**
     * Returns TRUE if every character in text is printable, but neither letter, digit or blank, FALSE otherwise.
     *
     * @see https://php.net/ctype-punct
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_punct($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^!-\/\:-@\[-`\{-~]/', $text);
    }

    /**
     * Returns TRUE if every character in text creates some sort of white space, FALSE otherwise. Besides the blank character this also includes tab, vertical tab, line feed, carriage return and form feed characters.
     *
     * @see https://php.net/ctype-space
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_space($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^\s]/', $text);
    }

    /**
     * Returns TRUE if every character in text is an uppercase letter.
     *
     * @see https://php.net/ctype-upper
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_upper($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^A-Z]/', $text);
    }

    /**
     * Returns TRUE if every character in text is a hexadecimal 'digit', that is a decimal digit or a character from [A-Fa-f] , FALSE otherwise.
     *
     * @see https://php.net/ctype-xdigit
     *
<<<<<<< HEAD
     * @param string|int $text
=======
     * @param mixed $text
>>>>>>> id3-tag
     *
     * @return bool
     */
    public static function ctype_xdigit($text)
    {
<<<<<<< HEAD
        $text = self::convert_int_to_char_for_ctype($text);
=======
        $text = self::convert_int_to_char_for_ctype($text, __FUNCTION__);
>>>>>>> id3-tag

        return \is_string($text) && '' !== $text && !preg_match('/[^A-Fa-f0-9]/', $text);
    }

    /**
     * Converts integers to their char versions according to normal ctype behaviour, if needed.
     *
     * If an integer between -128 and 255 inclusive is provided,
     * it is interpreted as the ASCII value of a single character
     * (negative values have 256 added in order to allow characters in the Extended ASCII range).
     * Any other integer is interpreted as a string containing the decimal digits of the integer.
     *
<<<<<<< HEAD
     * @param string|int $int
     *
     * @return mixed
     */
    private static function convert_int_to_char_for_ctype($int)
=======
     * @param mixed  $int
     * @param string $function
     *
     * @return mixed
     */
    private static function convert_int_to_char_for_ctype($int, $function)
>>>>>>> id3-tag
    {
        if (!\is_int($int)) {
            return $int;
        }

        if ($int < -128 || $int > 255) {
            return (string) $int;
        }

<<<<<<< HEAD
=======
        if (\PHP_VERSION_ID >= 80100) {
            @trigger_error($function.'(): Argument of type int will be interpreted as string in the future', \E_USER_DEPRECATED);
        }

>>>>>>> id3-tag
        if ($int < 0) {
            $int += 256;
        }

        return \chr($int);
    }
}
