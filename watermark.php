<?php

// ============================================
// LAYER 1: Zero-width character encoding
// Encodes user ID in binary using invisible characters
// ============================================

function encodeZeroWidth($text, $userId) {
    // Convert user ID to binary string
    $binary = decbin($userId);
    // Pad to 16 bits
    $binary = str_pad($binary, 16, '0', STR_PAD_LEFT);
    
    // Zero-width space = 1, Zero-width non-joiner = 0
    $zwsp = "\u{200B}";   // invisible character for 1
    $zwnj = "\u{200C}";   // invisible character for 0
    
    // Build invisible signature
    $signature = '';
    foreach (str_split($binary) as $bit) {
        $signature .= ($bit === '1') ? $zwsp : $zwnj;
    }
    
    // Insert signature after first word
    $words = explode(' ', $text, 2);
    return $words[0] . $signature . ' ' . $words[1];
}

// ============================================
// LAYER 2: Synonym substitution encoding
// Survives OCR and print/scan attacks
// ============================================

$synonymPairs = [
    ['happy',  'glad'],
    ['big',    'large'],
    ['fast',   'quick'],
    ['smart',  'clever'],
    ['show',   'display'],
    ['make',   'create'],
    ['start',  'begin'],
    ['end',    'finish'],
    ['look',   'appear'],
    ['need',   'require'],
    ['use',    'utilize'],
    ['help',   'assist'],
    ['find',   'locate'],
    ['keep',   'retain'],
    ['give',   'provide'],
    ['get',    'obtain'],
];

function encodeSynonyms($text, $userId) {
    global $synonymPairs;
    
    $binary = decbin($userId);
    $binary = str_pad($binary, 16, '0', STR_PAD_LEFT);
    $bits = str_split($binary);
    $bitIndex = 0;
    
    foreach ($synonymPairs as $pair) {
        if ($bitIndex >= count($bits)) break;
        
        $bit = $bits[$bitIndex];
        // 0 = use first synonym, 1 = use second synonym
        $useWord   = ($bit === '0') ? $pair[0] : $pair[1];
        $replaceWord = ($bit === '0') ? $pair[1] : $pair[0];
        
        // Replace case-insensitively
        $text = preg_replace('/\b' . $replaceWord . '\b/i', $useWord, $text);
        $bitIndex++;
    }
    
    return $text;
}

// ============================================
// COMBINED WATERMARK
// Applies both layers
// ============================================

function watermarkText($text, $userId) {
    $text = encodeSynonyms($text, $userId);
    $text = encodeZeroWidth($text, $userId);
    return $text;
}

// ============================================
// DECODER - reveals user ID from watermarked text
// ============================================

function decodeZeroWidth($text) {
    $zwsp = "\u{200B}";
    $zwnj = "\u{200C}";
    
    $binary = '';
    foreach (mb_str_split($text) as $char) {
        if ($char === $zwsp) $binary .= '1';
        if ($char === $zwnj) $binary .= '0';
    }
    
    if (strlen($binary) < 16) return null;
    return bindec(substr($binary, 0, 16));
}

function decodeSynonyms($text) {
    global $synonymPairs;
    
    $binary = '';
    foreach ($synonymPairs as $pair) {
        // Check which synonym is present
        if (preg_match('/\b' . $pair[0] . '\b/i', $text)) {
            $binary .= '0';
        } elseif (preg_match('/\b' . $pair[1] . '\b/i', $text)) {
            $binary .= '1';
        } else {
            $binary .= '0'; // default
        }
    }
    
    if (strlen($binary) < 16) return null;
    return bindec(substr($binary, 0, 16));
}

// ============================================
// TEST IT
// ============================================

$originalText = "This is a book about art. Artists are happy people who make big and fast work. They need to show their talent and help others find beauty. Smart artists keep creating and give the world something special.";

$userId = 42; // pretend this is the buyer's ID from your database

echo "<h2>Original Text:</h2>";
echo "<p>" . $originalText . "</p>";

$watermarked = watermarkText($originalText, $userId);

echo "<h2>Watermarked Text (looks the same to reader):</h2>";
echo "<p>" . $watermarked . "</p>";

echo "<h2>Decoded User ID (from zero-width):</h2>";
echo "<p>" . decodeZeroWidth($watermarked) . "</p>";

echo "<h2>Decoded User ID (from synonyms - OCR resistant):</h2>";
echo "<p>" . decodeSynonyms($watermarked) . "</p>";
?>