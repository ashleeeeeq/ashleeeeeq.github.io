-- Create Database
CREATE DATABASE IF NOT EXISTS petalgram_flowershop_db;
USE petalgram_flowershop_db;

-- ============================================
-- USERS TABLE
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    mobile VARCHAR(20) NOT NULL,
    role ENUM('admin','customer') DEFAULT 'customer',
    profile_picture VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert admin account (password: admin123)
INSERT INTO users (fullname, email, password, address, mobile, role) 
SELECT 'Admin User', 'admin@petalgram.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Office, Manila', '09123456789', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@petalgram.com');

-- ============================================
-- PRODUCTS TABLE (only base products – variations will go into product_variations)
-- ============================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255),
    category VARCHAR(100),
    featured ENUM('none','new','trending','best_seller') DEFAULT 'none',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert all base products (each group becomes one product)
INSERT INTO products (product_name, description, price, stock, image, category, featured) VALUES
-- 1. Bouquets – Classic Rose Bouquet (base: Dozen)
('Classic Rose Bouquet', 'Hand-tied red rose bouquet wrapped in kraft paper. Choose a dozen, two dozen, or premium 36-stem arrangement.', 750.00, 100, 'rose-bouquet.jpg', 'Bouquets', 'best_seller'),

-- 2. Bouquets – Sunflower Bundle (base: Half Dozen)
('Sunny Sunflower Bundle', 'Bright sunflowers tied with a satin ribbon for an instant mood lift. Available in half dozen, dozen, and deluxe sizes.', 550.00, 80, 'sunflower-bundle.jpg', 'Bouquets', 'new'),

-- 3. Bouquets – Mixed Seasonal Bouquet (base: Classic)
('Mixed Seasonal Bouquet', 'A florist''s choice of the freshest seasonal blooms. Available in Petite, Classic, and Grand sizes.', 650.00, 90, 'seasonal-bouquet.jpg', 'Bouquets', 'trending'),

-- 4. Bouquets – Tulip Bouquet (base: 10 stems)
('Tulip Bouquet', 'Soft pastel tulips imported fresh from the highlands. Available in 10, 20, and 30 stem bunches.', 500.00, 70, 'tulip-bouquet.jpg', 'Bouquets', 'none'),

-- 5. Bouquets – Peony & Pastel Bouquet (base: Small)
('Peony & Pastel Bouquet', 'Romantic peonies and blush blooms for weddings and anniversaries. Available in Small, Medium, and Large.', 1250.00, 40, 'peony-bouquet.jpg', 'Bouquets', 'best_seller'),

-- 6. Flowers – Carnation Bunch (base: Dozen)
('Carnation Bunch', 'Fresh long-stem carnations in mixed colors. Available in dozen, two dozen, and three dozen bunches.', 380.00, 120, 'carnation-bunch.jpg', 'Flowers', 'new'),

-- 7. Flowers – Orchid Stem (base: Single stem)
('Fresh Orchid Stem', 'Elegant phalaenopsis orchid cut stem, perfect for minimalist vases. Available as single stem, pack of 3, or pack of 5.', 420.00, 60, 'orchid-stem.jpg', 'Flowers', 'none'),

-- 8. Flowers – Daisy Bunch (base: Dozen)
('Daisy Bunch', 'Cheerful white and yellow daisies that brighten any room. Available in dozen and two dozen bunches.', 320.00, 110, 'daisy-bunch.jpg', 'Flowers', 'trending'),

-- 9. Flowers – Baby''s Breath (base: Small bunch)
('Baby''s Breath Bunch', 'Airy gypsophila for bouquets, vases, and event decor. Available in Small, Medium, and Large bunches.', 250.00, 95, 'babys-breath.jpg', 'Flowers', 'none'),

-- 10. Flowers – Gerbera Bunch (base: Half dozen)
('Gerbera Bunch', 'Vivid gerbera daisies straight from local farms. Available in half dozen and dozen bunches.', 360.00, 85, 'gerbera-bunch.jpg', 'Flowers', 'best_seller'),

-- 11. Plants – Potted Rose Plant (base: 4 inch)
('Potted Rose Plant', 'Living rose plant in a ceramic pot. Available in 4-inch, 6-inch, and 8-inch grow pots.', 480.00, 75, 'potted-rose.jpg', 'Plants', 'best_seller'),

-- 12. Plants – Peace Lily (base: Small)
('Indoor Peace Lily', 'Low-maintenance peace lily that thrives indoors. Available in Small, Medium, and Large pots.', 550.00, 65, 'peace-lily.jpg', 'Plants', 'new'),

-- 13. Plants – Succulent Set (base: Set of 1)
('Succulent Set', 'Assorted rosette succulents in terracotta pots. Available as Set of 1, Set of 3, and Set of 5.', 290.00, 130, 'succulent-set.jpg', 'Plants', 'trending'),

-- 14. Plants – Snake Plant (base: 4 inch)
('Snake Plant', 'Hardy snake plant that purifies the air. Available in 4-inch, 6-inch, and 10-inch pots.', 450.00, 70, 'snake-plant.jpg', 'Plants', 'none'),

-- 15. Plants – Herb Grow Kit (base: 3 Herbs)
('Herb Grow Kit', 'Grow-your-own basil, mint, and more with soil, pots, and seeds. Available in 3-herb, 5-herb, and 7-herb kits.', 620.00, 55, 'herb-kit.jpg', 'Plants', 'none'),

-- 16. Gifts – Gift Hamper (base: Classic)
('Flower Gift Hamper', 'Bouquet plus snacks and a handwritten card in a woven basket. Available in Classic, Deluxe, and Premium.', 1500.00, 45, 'gift-hamper.jpg', 'Gifts', 'best_seller'),

-- 17. Gifts – Flower & Chocolate Box (base: 6 pcs)
('Flower & Chocolate Box', 'Fresh roses paired with artisan chocolates. Available in 6-piece, 12-piece, and 24-piece boxes.', 980.00, 60, 'flower-chocolate.jpg', 'Gifts', 'trending'),

-- 18. Gifts – Dried Flower Arrangement (base: Small)
('Dried Flower Arrangement', 'Preserved bunny tails, statice, and palm in a ceramic vase. Available in Small, Medium, and Large.', 850.00, 50, 'dried-arrangement.jpg', 'Gifts', 'new'),

-- 19. Gifts – Scented Candle & Bloom Set (base: Regular)
('Scented Candle & Bloom Set', 'A blooming candle paired with a mini bouquet and lighter. Available in Regular and Deluxe sets.', 720.00, 65, 'candle-bloom.jpg', 'Gifts', 'none'),

-- 20. Gifts – Personalized Gift Card (base: 500)
('Personalized Gift Card', 'Let them choose their favorite blooms. Available in 500, 1000, and 2000 denominations.', 500.00, 200, 'gift-card.jpg', 'Gifts', 'none');

-- ============================================
-- PRODUCT VARIATIONS TABLE
-- ============================================
CREATE TABLE product_variations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variation_name VARCHAR(100) NOT NULL,
    variation_value VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    sku VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_variation (product_id, variation_name, variation_value)
);

-- Insert variations for each base product
-- 1. Classic Rose Bouquet (product_id = 1)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(1, 'Size', 'Dozen (12 stems)', 750.00, 100),
(1, 'Size', 'Two Dozen (24 stems)', 1350.00, 70),
(1, 'Size', 'Premium (36 stems)', 1950.00, 40);

-- 2. Sunny Sunflower Bundle (product_id = 2)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(2, 'Size', 'Half Dozen', 550.00, 80),
(2, 'Size', 'Dozen', 950.00, 60),
(2, 'Size', 'Deluxe (18 stems)', 1350.00, 35);

-- 3. Mixed Seasonal Bouquet (product_id = 3)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(3, 'Size', 'Petite', 450.00, 90),
(3, 'Size', 'Classic', 650.00, 75),
(3, 'Size', 'Grand', 950.00, 45);

-- 4. Tulip Bouquet (product_id = 4)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(4, 'Stems', '10 Stems', 500.00, 70),
(4, 'Stems', '20 Stems', 900.00, 50),
(4, 'Stems', '30 Stems', 1250.00, 30);

-- 5. Peony & Pastel Bouquet (product_id = 5)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(5, 'Size', 'Small', 1250.00, 40),
(5, 'Size', 'Medium', 1750.00, 25),
(5, 'Size', 'Large', 2350.00, 15);

-- 6. Carnation Bunch (product_id = 6)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(6, 'Bunch', 'Dozen', 380.00, 120),
(6, 'Bunch', 'Two Dozen', 700.00, 80),
(6, 'Bunch', 'Three Dozen', 980.00, 50);

-- 7. Fresh Orchid Stem (product_id = 7)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(7, 'Pack', 'Single Stem', 420.00, 60),
(7, 'Pack', 'Pack of 3', 1150.00, 35),
(7, 'Pack', 'Pack of 5', 1800.00, 20);

-- 8. Daisy Bunch (product_id = 8)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(8, 'Bunch', 'Dozen', 320.00, 110),
(8, 'Bunch', 'Two Dozen', 580.00, 70);

-- 9. Baby's Breath Bunch (product_id = 9)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(9, 'Size', 'Small Bunch', 250.00, 95),
(9, 'Size', 'Medium Bunch', 400.00, 65),
(9, 'Size', 'Large Bunch', 600.00, 40);

-- 10. Gerbera Bunch (product_id = 10)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(10, 'Bunch', 'Half Dozen', 360.00, 85),
(10, 'Bunch', 'Dozen', 650.00, 55);

-- 11. Potted Rose Plant (product_id = 11)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(11, 'Pot Size', '4 Inch', 480.00, 75),
(11, 'Pot Size', '6 Inch', 680.00, 50),
(11, 'Pot Size', '8 Inch', 950.00, 30);

-- 12. Indoor Peace Lily (product_id = 12)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(12, 'Pot Size', 'Small', 550.00, 65),
(12, 'Pot Size', 'Medium', 850.00, 45),
(12, 'Pot Size', 'Large', 1250.00, 25);

-- 13. Succulent Set (product_id = 13)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(13, 'Set', 'Set of 1', 290.00, 130),
(13, 'Set', 'Set of 3', 780.00, 80),
(13, 'Set', 'Set of 5', 1200.00, 50);

-- 14. Snake Plant (product_id = 14)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(14, 'Pot Size', '4 Inch', 450.00, 70),
(14, 'Pot Size', '6 Inch', 650.00, 45),
(14, 'Pot Size', '10 Inch', 1100.00, 25);

-- 15. Herb Grow Kit (product_id = 15)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(15, 'Kit', '3 Herb Kit', 620.00, 55),
(15, 'Kit', '5 Herb Kit', 950.00, 35),
(15, 'Kit', '7 Herb Kit', 1280.00, 20);

-- 16. Flower Gift Hamper (product_id = 16)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(16, 'Tier', 'Classic', 1500.00, 45),
(16, 'Tier', 'Deluxe', 2200.00, 30),
(16, 'Tier', 'Premium', 3200.00, 15);

-- 17. Flower & Chocolate Box (product_id = 17)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(17, 'Box Size', '6 Pieces', 980.00, 60),
(17, 'Box Size', '12 Pieces', 1650.00, 40),
(17, 'Box Size', '24 Pieces', 2800.00, 20);

-- 18. Dried Flower Arrangement (product_id = 18)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(18, 'Size', 'Small', 850.00, 50),
(18, 'Size', 'Medium', 1250.00, 35),
(18, 'Size', 'Large', 1750.00, 20);

-- 19. Scented Candle & Bloom Set (product_id = 19)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(19, 'Set', 'Regular', 720.00, 65),
(19, 'Set', 'Deluxe', 1100.00, 40);

-- 20. Personalized Gift Card (product_id = 20)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(20, 'Denomination', 'PHP 500', 500.00, 200),
(20, 'Denomination', 'PHP 1000', 1000.00, 200),
(20, 'Denomination', 'PHP 2000', 2000.00, 200);

-- ============================================
-- CART TABLE
-- ============================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    variation_id INT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10,2) NULL,
    name VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variation_id) REFERENCES product_variations(id) ON DELETE SET NULL,
    UNIQUE KEY unique_user_product_variation (user_id, product_id, variation_id)
);

-- ============================================
-- ORDERS TABLE
-- ============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash','gcash','card') NOT NULL,
    delivery_method ENUM('pickup','delivery') NOT NULL,
    status ENUM('pending','confirmed','processing','ready_for_pickup','to_ship','shipped','out_for_delivery','completed','cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    pickup_date DATE NULL,
    pickup_time_slot VARCHAR(50) NULL,
    seller_confirmed_at TIMESTAMP NULL,
    ready_at TIMESTAMP NULL,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    picked_up_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================
-- ORDER ITEMS TABLE
-- ============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    variation_id INT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (variation_id) REFERENCES product_variations(id) ON DELETE SET NULL
);

-- ============================================
-- INDEXES
-- ============================================
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_product_category ON products(category);
CREATE INDEX idx_product_featured ON products(featured);
CREATE INDEX idx_order_user ON orders(user_id);
CREATE INDEX idx_order_status ON orders(status);
CREATE INDEX idx_order_date ON orders(created_at);
CREATE INDEX idx_cart_user ON cart(user_id);
CREATE INDEX idx_cart_variation ON cart(variation_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_variation ON order_items(variation_id);

-- ============================================
-- VERIFICATION
-- ============================================
SELECT 'DATABASE SETUP COMPLETE' as 'Status';
SELECT CONCAT('Users: ', COUNT(*)) as 'Users' FROM users;
SELECT CONCAT('Base Products: ', COUNT(*)) as 'Base Products' FROM products;
SELECT CONCAT('Variations: ', COUNT(*)) as 'Variations' FROM product_variations;
SELECT CONCAT('Categories: ', COUNT(DISTINCT category)) as 'Categories' FROM products;

-- ============================================
-- USERS TABLE - ADD VERIFICATION FIELDS
-- ============================================
-- First, check if columns exist and add them if they don't
-- Run these ALTER statements:

ALTER TABLE users 
ADD COLUMN IF NOT EXISTS verification_token VARCHAR(255) NULL AFTER password,
ADD COLUMN IF NOT EXISTS is_verified BOOLEAN DEFAULT FALSE AFTER verification_token,
ADD COLUMN IF NOT EXISTS verified_at DATETIME NULL AFTER is_verified;

-- Update existing admin to be verified
UPDATE users SET is_verified = 1, verified_at = NOW() WHERE email = 'admin@petalgram.com';
