<?php
/**
 * Sistemi i përkthimeve (shqip / anglisht).
 * Gjuha zgjidhet me ?lang=sq|en, ruhet në sesion + cookie 1-vjeçare.
 */

const SUPPORTED_LANGS = ['sq', 'en'];

function current_lang(): string
{
    static $lang = null;
    if ($lang !== null) {
        return $lang;
    }

    if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGS, true)) {
        $lang = $_GET['lang'];
        $_SESSION['lang'] = $lang;
        setcookie('lang', $lang, time() + 86400 * 365, '/');
        return $lang;
    }
    if (!empty($_SESSION['lang']) && in_array($_SESSION['lang'], SUPPORTED_LANGS, true)) {
        return $lang = $_SESSION['lang'];
    }
    if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], SUPPORTED_LANGS, true)) {
        return $lang = $_COOKIE['lang'];
    }
    return $lang = 'sq';
}

/** Kthen tekstin e përkthyer për çelësin e dhënë. */
function t(string $key): string
{
    static $strings = null;
    if ($strings === null) {
        $strings = require __DIR__ . '/../lang/' . current_lang() . '.php';
    }
    return $strings[$key] ?? $key;
}

/** Emri i muajit (1-12) në gjuhën aktuale. */
function month_name(int $m): string
{
    return t("month.$m");
}

/** Linku për ndërrimin e gjuhës duke ruajtur parametrat GET të faqes. */
function lang_url(string $lang): string
{
    $params = array_merge($_GET, ['lang' => $lang]);
    return '?' . http_build_query($params);
}

/** Butonat AL | EN. */
function lang_switch_html(): string
{
    $current = current_lang();
    $html = '<span class="lang-switch">';
    foreach (['sq' => 'AL', 'en' => 'EN'] as $code => $label) {
        $active = $current === $code ? ' active' : '';
        $html .= '<a class="lang-option' . $active . '" href="' . e(lang_url($code)) . '">' . $label . '</a>';
    }
    return $html . '</span>';
}
