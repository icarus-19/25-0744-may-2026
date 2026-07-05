<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - A.A.S</title>
    <link rel="stylesheet" href="styler.css">
</head>
<body>

<nav>
    <img src="assets/download png.webp" alt="AAS Logo" height="50">
    <a href="index.html">Home</a>
    <a href="shop.html">Shop</a>
    <a href="artists.html">Artists</a>
    <a href="contact.html">Contact</a>
</nav>

<div class="form-container">
    <h1>Create an Account</h1>
    <p>Join the A.A.S community</p>

    <form id="registerForm" method="POST" action="regi.php">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Enter your name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="cindy@gmail.com" required>

        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" placeholder="0712345678" required>

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="">Select Gender</option>
            <option value="female">Female</option>
            <option value="male">Male</option>
            <option value="other">Other</option>
        </select>

        <button type="submit">Submit</button>
    </form>

    <?php
    ini_set('display_errors', 1);
error_reporting(E_ALL);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $supabaseUrl = 'https://yfnbjqxuddyyvtbhqilx.supabase.co';
        $supabaseKey = 'sb_publishable_jH84J5GB4CFKieOv_8uzBw_nXw_ezvp';

        $data = [
            'name'   => $_POST['name'],
            'email'  => $_POST['email'],
            'phone'  => $_POST['phone'],
            'gender' => $_POST['gender'],
        ];

        $ch = curl_init($supabaseUrl . '/rest/v1/restras');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $supabaseKey,
            'Authorization: Bearer ' . $supabaseKey,
            'Content-Type: application/json',
            'Prefer: return=minimal'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        echo '<p>HTTP Code: ' . $httpCode . '</p>';
echo '<p>Response: ' . htmlspecialchars($response) . '</p>';

        if ($httpCode == 201) {
            echo '<p class="success-message" style="display:block;">Thank you, ' . htmlspecialchars($data['name']) . '! You have successfully registered.</p>';
        } else {
            echo '<p class="success-message" style="display:block; background-color:red;">Something went wrong. Please try again.</p>';
            
        }
        
    }
    ?>
</div>

<script src="script.js"></script>

</body>
</html>