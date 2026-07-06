<?php
// ... keep all your existing PHP functions here ...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A.A.S Watermark Tool</title>
    <link rel="stylesheet" href="styler.css">
    <style>
        .watermark-container {
            max-width: 800px;
            margin: 60px auto;
            padding: 40px;
        }
        .watermark-box {
            background-color: #ede4d3;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 25px;
            border-left: 5px solid #b5651d;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .watermark-box h2 {
            color: #3b2a1a;
            margin-bottom: 15px;
            font-family: Cambria;
        }
        .watermark-box p {
            color: #5a4a3f;
            line-height: 1.7;
        }
        .result-id {
            font-size: 2rem;
            font-weight: bold;
            color: #b5651d;
        }
        .hero-title {
            text-align: center;
            color: #3b2a1a;
            font-family: Cambria;
            font-size: 2rem;
            margin-bottom: 5px;
        }
        .hero-sub {
            text-align: center;
            color: #7a5c3a;
            margin-bottom: 40px;
            font-style: italic;
        }
        .tag {
            display: inline-block;
            background-color: #b5651d;
            color: white;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<nav>
    <img src="assets/download png.webp" alt="AAS Logo" height="50">
    <a href="index.html">Home</a>
    <a href="shop.html">Shop</a>
    <a href="artists.html">Artists</a>
    <a href="contact.html">Contact</a>
</nav>

<div class="watermark-container">
    <h1 class="hero-title">A.A.S Watermark Engine</h1>
    <p class="hero-sub">Protecting creators. Tracing pirates.</p>

    <div class="watermark-box">
        <span class="tag">Original</span>
        <h2>Original Text</h2>
        <p><?php echo $originalText; ?></p>
    </div>

    <div class="watermark-box">
        <span class="tag">Watermarked</span>
        <h2>Watermarked Text</h2>
        <p><?php echo $watermarked; ?></p>
        <p style="font-size:0.85rem; color:#b5651d; margin-top:10px;">
            ✅ Looks identical to the reader — watermark is invisible
        </p>
    </div>

    <div class="watermark-box">
        <span class="tag">Decoded — Digital</span>
        <h2>User ID (Zero-Width Layer)</h2>
        <p class="result-id"><?php echo decodeZeroWidth($watermarked); ?></p>
        <p>Detected from invisible characters — works on digital copies</p>
    </div>

    <div class="watermark-box">
        <span class="tag">Decoded — OCR Resistant</span>
        <h2>User ID (Synonym Layer)</h2>
        <p class="result-id"><?php echo decodeSynonyms($watermarked); ?></p>
        <p>Detected from word choice patterns — survives print and scan attacks</p>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
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