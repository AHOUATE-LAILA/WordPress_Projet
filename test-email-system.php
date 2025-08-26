<?php
/**
 * Test du système d'emails Gaisio
 * Placez ce fichier à la racine de votre WordPress et accédez-y via navigateur
 */

// Charger WordPress
require_once('wp-load.php');

// Vérifier que l'utilisateur est connecté et a les permissions
if (!current_user_can('manage_options')) {
    wp_die('Permissions insuffisantes');
}

echo '<h1>🧪 Test du Système d\'Emails Gaisio</h1>';

// Test 1: Vérifier que la classe existe
if (class_exists('GaisioEarthquakeManager')) {
    echo '<p>✅ Classe GaisioEarthquakeManager trouvée</p>';
    
    // Test 2: Vérifier que la fonction d'envoi d'email existe
    $gaisio = new GaisioEarthquakeManager();
    
    if (method_exists($gaisio, 'send_login_credentials_email')) {
        echo '<p>✅ Méthode send_login_credentials_email trouvée</p>';
        
        // Test 3: Essayer d'envoyer un email de test
        echo '<h2>📧 Test d\'envoi d\'email</h2>';
        
        $test_email = 'test@example.com'; // Remplacez par votre email
        $test_name = 'Utilisateur Test';
        $test_username = 'testuser';
        $test_password = 'testpass123';
        
        echo "<p>Envoi d'un email de test à: {$test_email}</p>";
        
        // Utiliser la réflexion pour accéder à la méthode privée
        $reflection = new ReflectionClass($gaisio);
        $method = $reflection->getMethod('send_login_credentials_email');
        $method->setAccessible(true);
        
        try {
            $result = $method->invoke($gaisio, $test_email, $test_name, $test_username, $test_password);
            
            if ($result) {
                echo '<p style="color: green;">✅ Email envoyé avec succès !</p>';
            } else {
                echo '<p style="color: orange;">⚠️ Email non envoyé - vérifiez la configuration</p>';
            }
        } catch (Exception $e) {
            echo '<p style="color: red;">❌ Erreur lors de l\'envoi: ' . $e->getMessage() . '</p>';
        }
        
    } else {
        echo '<p style="color: red;">❌ Méthode send_login_credentials_email non trouvée</p>';
    }
    
    // Test 4: Vérifier les méthodes d'envoi alternatives
    $methods = ['send_email_with_wp_mail', 'send_email_with_phpmailer', 'send_email_with_php_mail'];
    
    echo '<h2>🔧 Méthodes d\'envoi disponibles</h2>';
    foreach ($methods as $method_name) {
        if (method_exists($gaisio, $method_name)) {
            echo "<p>✅ {$method_name} disponible</p>";
        } else {
            echo "<p style='color: red;'>❌ {$method_name} non disponible</p>";
        }
    }
    
} else {
    echo '<p style="color: red;">❌ Classe GaisioEarthquakeManager non trouvée</p>';
}

// Test 5: Vérifier la configuration WordPress
echo '<h2>⚙️ Configuration WordPress</h2>';
echo '<p>Site URL: ' . get_site_url() . '</p>';
echo '<p>Site Name: ' . get_bloginfo('name') . '</p>';
echo '<p>Admin Email: ' . get_option('admin_email') . '</p>';

// Test 6: Vérifier les fonctions d'email
echo '<h2>📧 Fonctions d\'email disponibles</h2>';
if (function_exists('wp_mail')) {
    echo '<p>✅ wp_mail() disponible</p>';
} else {
    echo '<p style="color: red;">❌ wp_mail() non disponible</p>';
}

if (function_exists('mail')) {
    echo '<p>✅ mail() PHP disponible</p>';
} else {
    echo '<p style="color: red;">❌ mail() PHP non disponible</p>';
}

// Test 7: Vérifier PHPMailer
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo '<p>✅ PHPMailer disponible</p>';
} else {
    echo '<p style="color: orange;">⚠️ PHPMailer non disponible</p>';
}

echo '<hr>';
echo '<p><strong>💡 Conseil:</strong> Si les emails ne sont pas envoyés, vérifiez la configuration SMTP de votre serveur XAMPP.</p>';
echo '<p><strong>🔧 Alternative:</strong> Utilisez un service comme SendGrid, Mailgun ou configurez Gmail SMTP.</p>';
?>
