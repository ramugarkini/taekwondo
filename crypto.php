<?php
function encrypt($plaintext, $key) {
    // Generate a random IV (Initialization Vector)
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($iv_length);
    
    // Encrypt the data
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, 0, $iv);
    
    // Combine ciphertext and IV and convert to hex
    $result = bin2hex($ciphertext . '::' . $iv);
    
    // Return a purely alphanumeric string
    return preg_replace('/[^a-zA-Z0-9]/', '', $result);
}

function decrypt($ciphertext, $key) {
    // Convert hex back to bytes
    $decoded = hex2bin($ciphertext);
    
    // Extract encrypted data and IV
    list($encrypted_data, $iv) = explode('::', $decoded, 2);
    
    // Decrypt the data
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
}

// Define a secret key (must be 32 bytes for AES-256)
$key = 'thisisaverysecretkeygeneratedbyramugarki1234567890'; // You should use a secure method to generate/store your key
?>
