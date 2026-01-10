<?php
// Database setup script for WOOD CONNECT with complete timber hub data
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'timber_connect';
$username = 'root';
$password = '';

try {
    // Connect to MySQL without selecting database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");
    
    echo "<h2>Database Setup for WOOD CONNECT</h2>";
    echo "Database created successfully!<br><br>";
    
    // Read and execute the schema file
    $schema_file = __DIR__ . '/schema.sql';
    if (file_exists($schema_file)) {
        $sql = file_get_contents($schema_file);
        
        // Split SQL statements
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        
        foreach ($statements as $statement) {
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                try {
                    $pdo->exec($statement);
                    if (preg_match('/CREATE TABLE/', $statement)) {
                        $table_name = preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $statement, $matches) ? $matches[1] : 'table';
                        echo "✓ Created table: $table_name<br>";
                    }
                } catch (PDOException $e) {
                    echo "Error executing statement: " . $e->getMessage() . "<br>";
                }
            }
        }
        
        echo "<br>✓ Database schema created successfully!<br><br>";
        
        // Add all timber marketers from your data
        $timber_hubs = [
            // Akure Timber Hubs
            [
                'business_name' => 'Orisun Ayo Planks Shield',
                'owner_name' => 'Proprietor',
                'address' => 'Adjacent Okiki Jesu filling Station, Oda-road, Akure Ondo State',
                'city' => 'Akure',
                'state' => 'Ondo',
                'local_government' => 'Akure South',
                'phone' => '08034227144',
                'email' => 'orisunayo@timber.com',
                'inventory' => [
                    ['species' => 'Albizia zygia', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 400],
                    ['species' => 'Sacoglottis gabonensis', 'dimensions' => '2x12', 'price' => 7000, 'quantity' => 800],
                    ['species' => 'Milicia excelsa', 'dimensions' => '1x6', 'price' => 2000, 'quantity' => 500],
                    ['species' => 'Pterocarpus osun', 'dimensions' => '1x12', 'price' => 4500, 'quantity' => 400],
                    ['species' => 'Funtumia elastica', 'dimensions' => '3x4', 'price' => 8000, 'quantity' => 400],
                    ['species' => 'Khaya ivorensis', 'dimensions' => '3x6', 'price' => 12000, 'quantity' => 400],
                    ['species' => 'Piptadeniastrum africanum', 'dimensions' => '2x4', 'price' => 2000, 'quantity' => 400],
                    ['species' => 'Afzelia africana', 'dimensions' => '2x3', 'price' => 1500, 'quantity' => 400]
                ]
            ],
            [
                'business_name' => 'Asejere (Adelanke) Plank Seller Shield',
                'owner_name' => 'Proprietor',
                'address' => 'Road block Ibadan-road, Akure South local Government, Ondo State',
                'city' => 'Akure',
                'state' => 'Ondo',
                'local_government' => 'Akure South',
                'phone' => '07025685033',
                'email' => 'asejere@timber.com',
                'inventory' => [
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x3', 'price' => 1400, 'quantity' => 500],
                    ['species' => 'Cordia platythyrsa', 'dimensions' => '2x2', 'price' => 1200, 'quantity' => 1200],
                    ['species' => 'Hallea ciliata', 'dimensions' => '2x2', 'price' => 7000, 'quantity' => 700],
                    ['species' => 'Albizia zygia', 'dimensions' => '2x6', 'price' => 3200, 'quantity' => 1000],
                    ['species' => 'Ceiba pentandra', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 800],
                    ['species' => 'Nauclea diderrichii', 'dimensions' => '3x4', 'price' => 2300, 'quantity' => 1000],
                    ['species' => 'Lophira alata', 'dimensions' => '2x8', 'price' => 1000, 'quantity' => 1000]
                ]
            ],
            [
                'business_name' => 'Love and Peace Plank Market',
                'owner_name' => 'Proprietor',
                'address' => 'Ayeni 2, custom Ondo-road, Akure South local Government Ondo State',
                'city' => 'Akure',
                'state' => 'Ondo',
                'local_government' => 'Akure South',
                'phone' => '08101133721',
                'email' => 'lovepeace@timber.com',
                'inventory' => [
                    ['species' => 'Balanites aegyptiaca', 'dimensions' => '1x12', 'price' => 2600, 'quantity' => 150],
                    ['species' => 'Balanites aegyptiaca', 'dimensions' => '3x4', 'price' => 2700, 'quantity' => 100],
                    ['species' => 'Balanites aegyptiaca', 'dimensions' => '2x4', 'price' => 1500, 'quantity' => 200],
                    ['species' => 'Balanites aegyptiaca', 'dimensions' => '2x2', 'price' => 1200, 'quantity' => 500],
                    ['species' => 'Afzelia africana', 'dimensions' => '2x4', 'price' => 1500, 'quantity' => 500],
                    ['species' => 'Ceiba pentandra', 'dimensions' => '1x12', 'price' => 2600, 'quantity' => 500]
                ]
            ],
            [
                'business_name' => 'WeliWeli / Anu Oluwapo Plank Marketer',
                'owner_name' => 'Proprietor',
                'address' => 'Oda road Power line, Akure South local Government, Akure Ondo State',
                'city' => 'Akure',
                'state' => 'Ondo',
                'local_government' => 'Akure South',
                'phone' => '08107215335',
                'email' => 'weliweli@timber.com',
                'inventory' => [
                    ['species' => 'Nauclea diderrichii', 'dimensions' => '1x12', 'price' => 2600, 'quantity' => 500],
                    ['species' => 'Pterocarpus osun', 'dimensions' => '1x12', 'price' => 4000, 'quantity' => 500],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x2', 'price' => 1000, 'quantity' => 500],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x3', 'price' => 1400, 'quantity' => 500],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x4', 'price' => 1500, 'quantity' => 500],
                    ['species' => 'Milicia excelsa', 'dimensions' => '3x4', 'price' => 2700, 'quantity' => 500]
                ]
            ],
            [
                'business_name' => 'God\'s Grace Plank Seller',
                'owner_name' => 'Proprietor',
                'address' => 'St Luke planks market, Akure South local Government, Ondo State',
                'city' => 'Akure',
                'state' => 'Ondo',
                'local_government' => 'Akure South',
                'phone' => '08167170106',
                'email' => 'godsgrace@timber.com',
                'inventory' => [
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x3', 'price' => 1400, 'quantity' => 500],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x12', 'price' => 6000, 'quantity' => 1000],
                    ['species' => 'Cordia platythyrsa', 'dimensions' => '2x2', 'price' => 1200, 'quantity' => 1200],
                    ['species' => 'Albizia zygia', 'dimensions' => '2x6', 'price' => 3500, 'quantity' => 1000],
                    ['species' => 'Ceiba pentandra', 'dimensions' => '3x4', 'price' => 3000, 'quantity' => 500],
                    ['species' => 'Ceiba pentandra', 'dimensions' => '3x8', 'price' => 6000, 'quantity' => 400],
                    ['species' => 'Nauclea diderrichii', 'dimensions' => '1x12', 'price' => 2600, 'quantity' => 500],
                    ['species' => 'Balanites aegyptiaca', 'dimensions' => '2x4', 'price' => 1500, 'quantity' => 1000]
                ]
            ],
            // Ekiti Timber Hubs
            [
                'business_name' => 'Mama Aduke Oladele Planks Market',
                'owner_name' => 'Mama Aduke Oladele',
                'address' => 'Ewenla Planks Market, Ado Local Government',
                'city' => 'Ado Ekiti',
                'state' => 'Ekiti',
                'local_government' => 'Ado Ekiti',
                'phone' => '08103123420',
                'email' => 'mamaaduke@timber.com',
                'inventory' => [
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x12', 'price' => 5000, 'quantity' => 300],
                    ['species' => 'Khaya ivorensis', 'dimensions' => '1x6', 'price' => 2500, 'quantity' => 300],
                    ['species' => 'Guarea thompsonii', 'dimensions' => '3x4', 'price' => 2000, 'quantity' => 300],
                    ['species' => 'Gmelina arborea', 'dimensions' => '2x4', 'price' => 3000, 'quantity' => 300],
                    ['species' => 'Tectona grandis', 'dimensions' => '1x12', 'price' => 4000, 'quantity' => 300],
                    ['species' => 'Pterocarpus osun', 'dimensions' => '3x8', 'price' => 3000, 'quantity' => 300],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x6', 'price' => 1500, 'quantity' => 300]
                ]
            ],
            [
                'business_name' => 'Egbewa Planks Market (Association)',
                'owner_name' => 'Association Chairman',
                'address' => 'Egbewa Planks Market, Ado Local Government',
                'city' => 'Ado Ekiti',
                'state' => 'Ekiti',
                'local_government' => 'Ado Ekiti',
                'phone' => '08012345678',
                'email' => 'egbewa@timber.com',
                'inventory' => [
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x12', 'price' => 5000, 'quantity' => 1000],
                    ['species' => 'Khaya ivorensis', 'dimensions' => '1x12', 'price' => 5000, 'quantity' => 1000],
                    ['species' => 'Terminalia ivorensis', 'dimensions' => '1x12', 'price' => 4000, 'quantity' => 1000],
                    ['species' => 'Terminalia superba', 'dimensions' => '2x6', 'price' => 2500, 'quantity' => 1000],
                    ['species' => 'Triplochiton scleroxylon', 'dimensions' => '2x4', 'price' => 1200, 'quantity' => 1000],
                    ['species' => 'Guarea thompsonii', 'dimensions' => '2x3', 'price' => 1100, 'quantity' => 1000],
                    ['species' => 'Funtumia elastica', 'dimensions' => '1x12', 'price' => 4000, 'quantity' => 1000],
                    ['species' => 'Albizia zygia', 'dimensions' => '2x6', 'price' => 2500, 'quantity' => 1000]
                ]
            ],
            // Ibadan Timber Hubs
            [
                'business_name' => 'Ademola Oluwaseun Planks Seller',
                'owner_name' => 'Ademola Oluwaseun',
                'address' => 'Orisumbare Planks Market, Bodija Isopako Ibadan North LG Oyo State',
                'city' => 'Ibadan',
                'state' => 'Oyo',
                'local_government' => 'Ibadan North',
                'phone' => '08030569398',
                'email' => 'ademola@timber.com',
                'inventory' => [
                    ['species' => 'Triplochiton scleroxylon', 'dimensions' => '2x2', 'price' => 800, 'quantity' => 800],
                    ['species' => 'Celtis mildbraedii', 'dimensions' => '2x3', 'price' => 1000, 'quantity' => 800],
                    ['species' => 'Ceiba pentandra', 'dimensions' => '2x4', 'price' => 1200, 'quantity' => 800],
                    ['species' => 'Albizia zygia', 'dimensions' => '2x6', 'price' => 2400, 'quantity' => 800],
                    ['species' => 'Terminalia ivorensis', 'dimensions' => '2x12', 'price' => 15000, 'quantity' => 800],
                    ['species' => 'Triplochiton scleroxylon', 'dimensions' => '2x8', 'price' => 5000, 'quantity' => 800],
                    ['species' => 'Entandrophragma cylindricum', 'dimensions' => '1x12', 'price' => 4800, 'quantity' => 800],
                    ['species' => 'Cordia millenii', 'dimensions' => '2x4', 'price' => 1200, 'quantity' => 500],
                    ['species' => 'Cordia millenii', 'dimensions' => '2x6', 'price' => 2400, 'quantity' => 500],
                    ['species' => 'Cordia millenii', 'dimensions' => '2x8', 'price' => 5000, 'quantity' => 400],
                    ['species' => 'Cordia millenii', 'dimensions' => '2x12', 'price' => 15000, 'quantity' => 300]
                ]
            ],
            // Lagos Timber Hubs
            [
                'business_name' => 'Alhaji Hon. Tajudeen Karem Timber Hub',
                'owner_name' => 'Alhaji Hon. Tajudeen Karem',
                'address' => 'Akin Ogungbile market, Alimosho Local Government, Lagos State',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'local_government' => 'Alimosho',
                'phone' => '08056568406',
                'email' => 'tajudeen@timber.com',
                'inventory' => [
                    ['species' => 'Albizia zygia', 'dimensions' => '1x12', 'price' => 1000, 'quantity' => 1000],
                    ['species' => 'Hallea ciliata', 'dimensions' => '2x12', 'price' => 2000, 'quantity' => 1000],
                    ['species' => 'Khaya ivorensis', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 500],
                    ['species' => 'Terminalia superba', 'dimensions' => '2x6', 'price' => 2500, 'quantity' => 500],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x6', 'price' => 2800, 'quantity' => 500],
                    ['species' => 'Sacoglottis gabonensis', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 500]
                ]
            ],
            [
                'business_name' => 'Alaga Planks Market',
                'owner_name' => 'Proprietor',
                'address' => 'Orisumbare plank market Idimu, Alimosho Local Government, Lagos',
                'city' => 'Lagos',
                'state' => 'Lagos',
                'local_government' => 'Alimosho',
                'phone' => '08024234320',
                'email' => 'alaga@timber.com',
                'inventory' => [
                    ['species' => 'Hallea ciliata', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 1000],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x12', 'price' => 10000, 'quantity' => 1000],
                    ['species' => 'Albizia zygia', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 1000],
                    ['species' => 'Terminalia ivorensis', 'dimensions' => '1x12', 'price' => 7000, 'quantity' => 1000],
                    ['species' => 'Ficus exasperata', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 1000],
                    ['species' => 'Sacoglottis gabonensis', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 1000],
                    ['species' => 'Funtumia elastica', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 1000],
                    ['species' => 'Anogeissus leiocarpus', 'dimensions' => '2x6', 'price' => 3000, 'quantity' => 1000],
                    ['species' => 'Brachystegia spp', 'dimensions' => '2x12', 'price' => 3000, 'quantity' => 1000]
                ]
            ],
            // Osogbo Timber Hubs
            [
                'business_name' => 'Alaga Eyenkorin Planks Seller',
                'owner_name' => 'Proprietor',
                'address' => 'Akorede Central Planks Market, Egbedore local Government, Osun State',
                'city' => 'Osogbo',
                'state' => 'Osun',
                'local_government' => 'Egbedore',
                'phone' => '08081897595',
                'email' => 'eyenkorin@timber.com',
                'inventory' => [
                    ['species' => 'Khaya ivorensis', 'dimensions' => '2x12', 'price' => 6000, 'quantity' => 250],
                    ['species' => 'Albizia zygia', 'dimensions' => '1x12', 'price' => 4500, 'quantity' => 350],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x4', 'price' => 2000, 'quantity' => 350],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x6', 'price' => 2450, 'quantity' => 200],
                    ['species' => 'Gmelina arborea', 'dimensions' => '2x3', 'price' => 1500, 'quantity' => 750],
                    ['species' => 'Anogeissus leiocarpus', 'dimensions' => '4x6', 'price' => 4000, 'quantity' => 550],
                    ['species' => 'Casia fistula', 'dimensions' => '3x4', 'price' => 2450, 'quantity' => 400],
                    ['species' => 'Tectona grandis', 'dimensions' => '2x4', 'price' => 1750, 'quantity' => 200]
                ]
            ],
            [
                'business_name' => 'Baba Adeyemo Alekuwodo Planks Market',
                'owner_name' => 'Baba Adeyemo',
                'address' => 'Akorede Plank Market, Egbedore local Government, Osogbo, Osun State',
                'city' => 'Osogbo',
                'state' => 'Osun',
                'local_government' => 'Egbedore',
                'phone' => '08034960040',
                'email' => 'adeyemo@timber.com',
                'inventory' => [
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x12', 'price' => 4800, 'quantity' => 500],
                    ['species' => 'Milicia excelsa', 'dimensions' => '2x12', 'price' => 8000, 'quantity' => 500],
                    ['species' => 'Khaya ivorensis', 'dimensions' => '3x4', 'price' => 4800, 'quantity' => 500],
                    ['species' => 'Khaya ivorensis', 'dimensions' => '2x12', 'price' => 6000, 'quantity' => 500],
                    ['species' => 'Gmelina arborea', 'dimensions' => '2x3', 'price' => 1000, 'quantity' => 500],
                    ['species' => 'Cordia millenii', 'dimensions' => '2x2', 'price' => 900, 'quantity' => 500],
                    ['species' => 'Pterocarpus osun', 'dimensions' => '2x12', 'price' => 4500, 'quantity' => 500],
                    ['species' => 'Terminalia ivorensis', 'dimensions' => '1x12', 'price' => 1500, 'quantity' => 500],
                    ['species' => 'Tectona grandis', 'dimensions' => '3x8', 'price' => 2400, 'quantity' => 500],
                    ['species' => 'Triplochiton scleroxylon', 'dimensions' => '1x12', 'price' => 2400, 'quantity' => 500]
                ]
            ]
        ];

        // Insert all timber hubs
        $total_marketers = 0;
        $total_inventory = 0;
        
        foreach ($timber_hubs as $hub) {
            // Insert marketer
            $stmt = $pdo->prepare("INSERT INTO marketers 
                (business_name, owner_name, address, city, state, local_government, phone, email, verification_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'verified')");
            
            $stmt->execute([
                $hub['business_name'],
                $hub['owner_name'],
                $hub['address'],
                $hub['city'],
                $hub['state'],
                $hub['local_government'],
                $hub['phone'],
                $hub['email']
            ]);
            
            $marketer_id = $pdo->lastInsertId();
            $total_marketers++;
            echo "✓ Added marketer: " . $hub['business_name'] . " (" . $hub['city'] . ", " . $hub['state'] . ")<br>";
            
            // Insert inventory for this marketer
            foreach ($hub['inventory'] as $item) {
                // Get species ID
                $species_stmt = $pdo->prepare("SELECT id FROM species WHERE scientific_name = ? OR JSON_CONTAINS(common_names, ?)");
                $species_stmt->execute([$item['species'], json_encode($item['species'])]);
                $species = $species_stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($species) {
                    $inventory_stmt = $pdo->prepare("INSERT INTO inventory 
                        (marketer_id, species_id, dimensions, price_per_unit, quantity_available, description) 
                        VALUES (?, ?, ?, ?, ?, ?)");
                    
                    $description = "Quality " . $item['species'] . " wood available in " . $item['dimensions'] . " dimensions";
                    
                    $inventory_stmt->execute([
                        $marketer_id,
                        $species['id'],
                        $item['dimensions'],
                        $item['price'],
                        $item['quantity'],
                        $description
                    ]);
                    
                    $total_inventory++;
                }
            }
            echo "&nbsp;&nbsp;Added " . count($hub['inventory']) . " inventory items<br>";
        }
        
        echo "<br><strong>Setup Completed Successfully!</strong><br>";
        echo "✓ Total Marketers Added: " . $total_marketers . "<br>";
        echo "✓ Total Inventory Items: " . $total_inventory . "<br>";
        echo "✓ Species Database: " . $pdo->query("SELECT COUNT(*) FROM species")->fetchColumn() . " species<br><br>";
        
        echo "<div class='alert alert-success mt-3'>";
        echo "<h4>🎉 WOOD CONNECT is Ready!</h4>";
        echo "<p>Your database has been populated with real timber market data from Akure, Ekiti, Ibadan, Lagos, and Osogbo.</p>";
        echo "<p><strong>Admin Login:</strong> admin / password</p>";
        echo "</div>";
        
        echo "<div class='mt-3'>";
        echo "<a href='/timber-connect/' class='btn btn-success me-2'>Go to Homepage</a>";
        echo "<a href='/timber-connect/marketplace/' class='btn btn-outline-success me-2'>Browse Marketplace</a>";
        echo "<a href='/timber-connect/login.php' class='btn btn-outline-primary'>Admin Login</a>";
        echo "</div>";
        
    } else {
        echo "<div class='alert alert-danger'>Schema file not found at: $schema_file</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . $e->getMessage() . "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
.alert { padding: 15px; margin: 10px 0; border-radius: 5px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.btn { display: inline-block; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 5px; }
.btn-success { background: #28a745; color: white; }
.btn-outline-success { border: 1px solid #28a745; color: #28a745; }
.btn-outline-primary { border: 1px solid #007bff; color: #007bff; }
</style>