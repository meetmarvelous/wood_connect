<?php
// Database setup script for WOOD CONNECT with complete timber hub data
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'wood_connect';
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
    
    // Define database schema
    $tables = [
        "admin_users" => "CREATE TABLE IF NOT EXISTS `admin_users` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `username` varchar(100) NOT NULL,
            `email` varchar(255) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `full_name` varchar(255) NOT NULL,
            `role` enum('super_admin','admin','moderator','marketer') DEFAULT 'admin',
            `is_active` tinyint(1) DEFAULT 1,
            `last_login` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `username` (`username`),
            UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "buyers" => "CREATE TABLE IF NOT EXISTS `buyers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `full_name` varchar(255) NOT NULL,
            `email` varchar(255) NOT NULL,
            `phone` varchar(20) NOT NULL,
            `password_hash` varchar(255) NOT NULL,
            `company_name` varchar(255) DEFAULT NULL,
            `address` text DEFAULT NULL,
            `city` varchar(100) DEFAULT NULL,
            `state` varchar(100) DEFAULT NULL,
            `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
            `last_login` timestamp NULL DEFAULT NULL,
            `is_active` tinyint(1) DEFAULT 1,
            `reset_token` varchar(100) DEFAULT NULL,
            `reset_expires` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `email` (`email`),
            KEY `idx_email` (`email`),
            KEY `idx_phone` (`phone`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",

        "marketers" => "CREATE TABLE IF NOT EXISTS `marketers` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `business_name` varchar(255) NOT NULL,
            `owner_name` varchar(255) NOT NULL,
            `address` text NOT NULL,
            `city` varchar(100) NOT NULL,
            `state` enum('Ondo','Ekiti','Osun','Oyo','Ogun','Lagos') NOT NULL,
            `local_government` varchar(100) NOT NULL,
            `phone` varchar(20) NOT NULL,
            `email` varchar(255) DEFAULT NULL,
            `password_hash` varchar(255) DEFAULT NULL,
            `business_description` text DEFAULT NULL,
            `verification_status` enum('pending','verified','rejected') DEFAULT 'verified',
            `verification_notes` text DEFAULT NULL,
            `profile_image` varchar(500) DEFAULT NULL,
            `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
            `last_login` timestamp NULL DEFAULT NULL,
            `is_active` tinyint(1) DEFAULT 1,
            `reset_token` varchar(100) DEFAULT NULL,
            `reset_expires` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_phone` (`phone`),
            KEY `idx_state_city` (`state`,`city`),
            KEY `idx_verification_status` (`verification_status`),
            KEY `idx_business_name` (`business_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "species" => "CREATE TABLE IF NOT EXISTS `species` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `scientific_name` varchar(255) NOT NULL,
            `common_names` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`common_names`)),
            `family` varchar(100) DEFAULT NULL,
            `density_range` varchar(100) DEFAULT NULL,
            `durability` varchar(50) DEFAULT NULL,
            `timber_value_rank` int(11) DEFAULT 1,
            `common_uses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`common_uses`)),
            `description` text DEFAULT NULL,
            `image_path` varchar(500) DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `scientific_name` (`scientific_name`),
            KEY `idx_scientific_name` (`scientific_name`),
            KEY `idx_durability` (`durability`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "platform_stats" => "CREATE TABLE IF NOT EXISTS `platform_stats` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `total_marketers` int(11) DEFAULT 0,
            `verified_marketers` int(11) DEFAULT 0,
            `total_species` int(11) DEFAULT 0,
            `active_listings` int(11) DEFAULT 0,
            `total_inquiries` int(11) DEFAULT 0,
            `completed_inquiries` int(11) DEFAULT 0,
            `stat_date` date NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `stat_date` (`stat_date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "inquiries" => "CREATE TABLE IF NOT EXISTS `inquiries` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `buyer_name` varchar(255) NOT NULL,
            `buyer_phone` varchar(20) NOT NULL,
            `buyer_email` varchar(255) DEFAULT NULL,
            `marketer_id` int(11) NOT NULL,
            `species_id` int(11) NOT NULL,
            `dimensions` varchar(50) NOT NULL,
            `quantity` int(11) NOT NULL,
            `message` text DEFAULT NULL,
            `status` enum('pending','contacted','completed','cancelled') DEFAULT 'pending',
            `admin_notes` text DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `species_id` (`species_id`),
            KEY `idx_marketer_status` (`marketer_id`,`status`),
            KEY `idx_created_at` (`created_at`),
            CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`marketer_id`) REFERENCES `marketers` (`id`) ON DELETE CASCADE,
            CONSTRAINT `inquiries_ibfk_2` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

        "inventory" => "CREATE TABLE IF NOT EXISTS `inventory` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `marketer_id` int(11) NOT NULL,
            `species_id` int(11) NOT NULL,
            `dimensions` varchar(50) NOT NULL,
            `price_per_unit` decimal(10,2) NOT NULL,
            `quantity_available` int(11) NOT NULL DEFAULT 0,
            `unit_type` enum('length','piece','bundle') DEFAULT 'length',
            `quality_grade` enum('premium','standard','economy') DEFAULT 'standard',
            `description` text DEFAULT NULL,
            `image_path` varchar(500) DEFAULT NULL,
            `is_available` tinyint(1) DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            KEY `species_id` (`species_id`),
            KEY `idx_marketer_species` (`marketer_id`,`species_id`),
            KEY `idx_dimensions` (`dimensions`),
            KEY `idx_price` (`price_per_unit`),
            KEY `idx_availability` (`is_available`),
            CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`marketer_id`) REFERENCES `marketers` (`id`) ON DELETE CASCADE,
            CONSTRAINT `inventory_ibfk_2` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    ];

    // Execute table creation
    foreach ($tables as $name => $sql) {
        try {
            $pdo->exec($sql);
            echo "✓ Created/Verified table: $name<br>";
        } catch (PDOException $e) {
            echo "Error creating table $name: " . $e->getMessage() . "<br>";
        }
    }

    // Insert Seed Data
    
    // 1. Admin User
    try {
        $admin_check = $pdo->query("SELECT COUNT(*) FROM admin_users WHERE username = 'admin'")->fetchColumn();
        if ($admin_check == 0) {
            $pdo->exec("INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `full_name`, `role`, `is_active`, `last_login`, `created_at`) VALUES
            ('admin', 'admin@woodconnect.com.ng', '$2y$10$4Gxnd777ol/UnmW5r5iJEuYeXB9VSajmc1bd3Qr616w6x1dY2GvBa', 'System Administrator', 'super_admin', 1, '2025-11-19 20:49:04', '2025-10-30 15:53:30')");
            echo "✓ Added default admin user<br>";
        }
    } catch (PDOException $e) {
        echo "Error adding admin user: " . $e->getMessage() . "<br>";
    }

    // 2. Species Data
    try {
        $species_check = $pdo->query("SELECT COUNT(*) FROM species")->fetchColumn();
        if ($species_check == 0) {
            $pdo->exec("INSERT INTO `species` (`id`, `scientific_name`, `common_names`, `family`, `density_range`, `durability`, `timber_value_rank`, `common_uses`, `description`, `image_path`, `created_at`, `updated_at`) VALUES
            (1, 'Milicia excelsa', '[\"Iroko\", \"Efo pupa\"]', 'Moraceae', '640-720 kg/m³', 'Very Durable', 1, '[\"Furniture\", \"Flooring\", \"Construction\", \"Boats\"]', 'Iroko is a large hardwood tree from the west coast of tropical Africa. It is one of the woods sometimes referred to as African Teak.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (2, 'Khaya ivorensis', '[\"Mahogany\", \"African Mahogany\"]', 'Meliaceae', '540-670 kg/m³', 'Durable', 2, '[\"Furniture\", \"Cabinetry\", \"Boat Building\", \"Veneer\"]', 'African Mahogany is a medium-sized tree native to tropical Africa. The wood is pinkish-brown and known for its workability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (3, 'Albizia zygia', '[\"Ayunre\", \"Ayinre\"]', 'Fabaceae', '560-640 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\", \"Plywood\"]', 'Commonly used for furniture and construction in West Africa.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (4, 'Pterocarpus osun', '[\"Sonu\", \"Opoporopo\", \"Ole\"]', 'Fabaceae', '700-800 kg/m³', 'Durable', 2, '[\"Furniture\", \"Flooring\", \"Carving\"]', 'Known for its durability and attractive grain pattern.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (5, 'Funtumia elastica', '[\"Ire\", \"Rubber tree\"]', 'Apocynaceae', '450-550 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Plywood\", \"Packaging\"]', 'Lightweight timber used for various wood products.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (6, 'Nauclea diderrichii', '[\"Opepe\", \"Bilinga\"]', 'Rubiaceae', '750-850 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Bridges\", \"Docks\"]', 'Known for its strength and durability in heavy construction.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (7, 'Ceiba pentandra', '[\"Araba\", \"Somi\", \"Ogungun\", \"White wood\"]', 'Malvaceae', '300-400 kg/m³', 'Perishable', 4, '[\"Plywood\", \"Packaging\", \"Light Construction\"]', 'Very lightweight wood, easy to work with but not durable.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (8, 'Terminalia ivorensis', '[\"Idigbo\", \"Black Afara\"]', 'Combretaceae', '560-640 kg/m³', 'Durable', 2, '[\"Furniture\", \"Flooring\", \"Construction\"]', 'Medium-weight timber with good durability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (9, 'Terminalia superba', '[\"Afara\", \"White Afara\", \"Limba\"]', 'Combretaceae', '500-580 kg/m³', 'Moderately Durable', 3, '[\"Veneer\", \"Plywood\", \"Furniture\"]', 'Light-colored wood used for veneer and furniture.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (10, 'Triplochiton scleroxylon', '[\"Obeche\", \"Arere\", \"Awa\", \"Ogbogbo\"]', 'Malvaceae', '380-450 kg/m³', 'Perishable', 4, '[\"Plywood\", \"Model Making\", \"Packaging\"]', 'Very lightweight and easy to carve.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (11, 'Cordia platythyrsa', '[\"Opoporopo\"]', 'Boraginaceae', '520-600 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Cabinetry\"]', 'Attractive wood with good workability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (12, 'Hallea ciliata', '[\"Abora\", \"Abura\"]', 'Rubiaceae', '550-650 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\"]', 'Medium-weight timber for general construction.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (13, 'Afzelia africana', '[\"Koko igbo\", \"Kokogbo\", \"Apa\"]', 'Fabaceae', '800-900 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Bridges\", \"Flooring\"]', 'Extremely durable and heavy wood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (14, 'Piptadeniastrum africanum', '[\"Ogbabi\", \"Dahoma\"]', 'Fabaceae', '680-780 kg/m³', 'Durable', 2, '[\"Construction\", \"Flooring\"]', 'Strong and durable construction timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (15, 'Sacoglottis gabonensis', '[\"Itara\"]', 'Humiriaceae', '850-950 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Marine Work\"]', 'Extremely dense and durable wood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (16, 'Tectona grandis', '[\"Teak\"]', 'Lamiaceae', '630-720 kg/m³', 'Extremely Durable', 1, '[\"Outdoor Furniture\", \"Decking\", \"Shipbuilding\"]', 'Premium timber known for exceptional durability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (17, 'Gmelina arborea', '[\"Gmelina\", \"Beechwood\"]', 'Lamiaceae', '430-510 kg/m³', 'Moderately Durable', 3, '[\"Plywood\", \"Furniture\", \"Pulpwood\"]', 'Fast-growing timber for various uses.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (18, 'Balanites aegyptiaca', '[\"Payapayaba\"]', 'Zygophyllaceae', '850-950 kg/m³', 'Very Durable', 1, '[\"Construction\", \"Tool Handles\"]', 'Very hard and durable wood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (19, 'Celtis mildbraedii', '[\"Ita\"]', 'Cannabaceae', '620-720 kg/m³', 'Durable', 2, '[\"Furniture\", \"Construction\"]', 'Good quality timber for furniture.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (20, 'Cordia millenii', '[\"Omo\"]', 'Boraginaceae', '520-600 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Cabinetry\"]', 'Attractive wood for fine furniture.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (21, 'Mansonia altissima', '[\"Mansonia\"]', 'Malvaceae', '560-640 kg/m³', 'Durable', 2, '[\"Furniture\", \"Flooring\"]', 'Medium-weight timber with good properties.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (22, 'Alstonia boonei', '[\"Orunmodun\", \"Pattern wood\"]', 'Apocynaceae', '420-480 kg/m³', 'Perishable', 4, '[\"Plywood\", \"Matchsticks\"]', 'Lightweight wood for specific uses.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (23, 'Guarea thompsonii', '[\"Obobo\", \"Black Guarea\"]', 'Meliaceae', '580-680 kg/m³', 'Durable', 2, '[\"Furniture\", \"Cabinetry\"]', 'Good quality timber for interior work.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (24, 'Anogeissus leiocarpus', '[\"Ayin\", \"Orin dudu\"]', 'Combretaceae', '900-1000 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Tool Handles\"]', 'Extremely hard and durable wood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (25, 'Ficus exasperata', '[\"Ipin\"]', 'Moraceae', '450-550 kg/m³', 'Perishable', 4, '[\"Plywood\", \"Packaging\"]', 'Lightweight wood for temporary uses.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (26, 'Parkia biglobosa', '[\"African locust bean\", \"Irugba\"]', 'Fabaceae', '700-800 kg/m³', 'Durable', 2, '[\"Construction\", \"Furniture\"]', 'Durable timber from the locust bean tree.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (27, 'Irvingia gabonensis', '[\"Oro\", \"Bush mango\"]', 'Irvingiaceae', '750-850 kg/m³', 'Durable', 2, '[\"Construction\", \"Furniture\"]', 'Hardwood from the bush mango tree.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (28, 'Brachystegia spp', '[\"Eku\"]', 'Fabaceae', '680-780 kg/m³', 'Durable', 2, '[\"Construction\", \"Flooring\"]', 'Durable African hardwood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (29, 'Cola gigantea', '[\"Ogbus\", \"Giant coal\"]', 'Malvaceae', '600-700 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\"]', 'Medium-weight timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (30, 'Cola nitida', '[\"Obi\", \"Kola\"]', 'Malvaceae', '580-680 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\"]', 'Timber from kola nut tree.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (31, 'Uapaca guineensis', '[\"Akun\"]', 'Phyllanthaceae', '750-850 kg/m³', 'Durable', 2, '[\"Construction\", \"Tool Handles\"]', 'Durable African hardwood.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (32, 'Spondias mombin', '[\"Iyeye\", \"Yellow mombin\"]', 'Anacardiaceae', '520-620 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Construction\"]', 'Medium-weight fruit tree timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (33, 'Daniellia oliveri', '[\"Iya\", \"African copaiba\"]', 'Fabaceae', '480-580 kg/m³', 'Moderately Durable', 3, '[\"Furniture\", \"Plywood\"]', 'Light to medium weight timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (34, 'Entandrophragma cylindricum', '[\"Okogbo\", \"Sapele\"]', 'Meliaceae', '620-720 kg/m³', 'Durable', 2, '[\"Furniture\", \"Veneer\"]', 'High-quality furniture timber.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (35, 'Lovoa trichilioides', '[\"Koko igbo\", \"African walnut\"]', 'Meliaceae', '520-620 kg/m³', 'Durable', 2, '[\"Furniture\", \"Cabinetry\"]', 'Beautiful wood for fine furniture.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (36, 'Lophira alata', '[\"Ekki\"]', 'Ochnaceae', '1050-1150 kg/m³', 'Extremely Durable', 1, '[\"Heavy Construction\", \"Marine Work\", \"Bridges\"]', 'Extremely dense and durable wood, resistant to termites.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (37, 'Casia fistula', '[\"Casia\"]', 'Fabaceae', '700-800 kg/m³', 'Durable', 2, '[\"Furniture\", \"Construction\"]', 'Durable timber with good workability.', NULL, '2025-10-30 15:53:29', '2025-10-30 15:53:29'),
            (38, 'Antiaris africana', '[\"False Iroko\"]', 'Moraceae', '450-550 kg/m³', 'Moderately Durable', 3, '[\"Plywood\", \"Packaging\"]', 'Lightweight wood resembling Iroko.', NULL, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
            (39, 'Erythrophleum suaveolens', '[\"Obo\"]', 'Fabaceae', '850-950 kg/m³', 'Very Durable', 1, '[\"Heavy Construction\", \"Bridges\"]', 'Extremely durable and heavy wood.', NULL, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
            (40, 'Azadirachta indica', '[\"Dongoyaro\", \"Neem\"]', 'Meliaceae', '650-750 kg/m³', 'Durable', 2, '[\"Furniture\", \"Construction\"]', 'Durable timber with medicinal properties.', NULL, '2025-10-30 15:53:30', '2025-10-30 15:53:30'),
            (41, 'Diospyros crassiflora', '[\"Black tree\", \"Gaboon ebony\"]', 'Ebenaceae', '950-1050 kg/m³', 'Extremely Durable', 1, '[\"Furniture\", \"Carving\", \"Musical Instruments\"]', 'Extremely dense and valuable ebony wood.', NULL, '2025-10-30 15:53:30', '2025-10-30 15:53:30')");
            echo "✓ Added species data<br>";
        }
    } catch (PDOException $e) {
        echo "Error adding species data: " . $e->getMessage() . "<br>";
    }

    echo "<br>✓ Database schema and seed data created successfully!<br><br>";
        
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