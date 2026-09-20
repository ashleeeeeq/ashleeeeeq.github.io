-- Create Database
CREATE DATABASE IF NOT EXISTS coco_coir_db;
USE coco_coir_db;

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
SELECT 'Admin User', 'admin@creatrix.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Office, Manila', '09123456789', 'admin'
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@creatrix.com');

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
-- 1. Gardening – Coir Pots (base: 3-inch)
('Biodegradable Coir Pots', 'Eco-friendly pots for seedlings. Choose your size: 3", 4", or 6".', 120.00, 200, 'coir-pot.jpg', 'Gardening', 'best_seller'),

-- 2. Gardening – Coir Grow Bags (base: Small 5kg)
('Coir Grow Bags', 'Compressed coir grow bags for container gardening. Available in Small (5kg), Medium (10kg), Large (15kg).', 180.00, 150, 'grow-bag.jpg', 'Gardening', 'new'),

-- 3. Gardening – Coir Poles (base: 2ft)
('Coir Poles', 'Natural coir poles for climbing plants. Available in 2ft, 3ft, 4ft lengths.', 250.00, 80, 'coir-pole.jpg', 'Gardening', 'none'),

-- 4. Gardening – Coco Peat Blocks (base: 650g)
('Coco Peat Blocks', 'Compressed coco peat blocks for soil conditioning. Available in 650g, 5kg, 10kg.', 95.00, 300, 'coco-peat.jpg', 'Gardening', 'trending'),

-- 5. Gardening – Coir Chips (base: 2L)
('Coir Chips', 'Coconut husk chips for orchids and epiphytes. Available in 2L and 5L bags.', 120.00, 150, 'coir-chips.jpg', 'Gardening', 'none'),

-- 6. Gardening – Coir Mulch (base: 10L)
('Coir Mulch', 'Natural coir mulch for garden beds. Available in 10L and 20L bags.', 180.00, 120, 'coir-mulch.jpg', 'Gardening', 'none'),

-- 7. Home & Living – Doormats (base: Small 40x60cm)
('Coir Doormats', 'Durable natural coir doormats. Available in Small (40x60cm), Large (50x80cm), Round (45cm).', 350.00, 100, 'doormat.jpg', 'Home & Living', 'best_seller'),

-- 8. Home & Living – Brushes (base: Kitchen)
('Coir Brushes', 'Natural coir brushes for various uses. Kitchen (small), Bath (medium), Floor (large).', 180.00, 150, 'coir-brush.jpg', 'Home & Living', 'new'),

-- 9. Home & Living – Scourers (base: Pack of 3)
('Coir Scourers', 'Biodegradable scourers for pots and pans. Available in Pack of 3 and Pack of 5.', 120.00, 200, 'coir-scourer.jpg', 'Home & Living', 'best_seller'),

-- 10. Home & Living – Baskets (base: Small 25cm)
('Coir Baskets', 'Handwoven coir storage baskets. Available in Small (25cm) and Large (35cm).', 280.00, 60, 'coir-basket.jpg', 'Home & Living', 'none'),

-- 11. Home & Living – Wall Hangings (base: Small)
('Coir Wall Hangings', 'Handmade coir wall decor. Available in Small (40x60cm) and Large (60x90cm).', 650.00, 30, 'wall-hanging.jpg', 'Home & Living', 'none'),

-- 12. Agriculture – Coir Slabs (base: Single slab)
('Coir Slabs', 'Premium coir slabs for hydroponic systems. Pre-washed and pH buffered.', 180.00, 200, 'coir-slabs.jpg', 'Agriculture', 'new'),

-- 13. Agriculture – Coir Discs (base: 8cm)
('Coir Discs', 'Coir discs for seed starting. Available in 8cm (50 pack), 10cm (30 pack), 12cm (20 pack).', 380.00, 150, 'coir-discs.jpg', 'Agriculture', 'best_seller'),

-- 14. Agriculture – Coir Fiber (base: 1kg)
('Coir Fiber', 'Raw coir fiber for mulching and crafts. Available in 1kg, 5kg, 10kg.', 150.00, 200, 'coir-fiber.jpg', 'Agriculture', 'none'),

-- 15. Packaging – Coir Sheets (base: 50x50cm)
('Coir Packaging Sheets', 'Natural coir sheets for eco-friendly packaging. Available in 50x50cm (10pk) and 70x100cm (5pk).', 280.00, 100, 'coir-sheets.jpg', 'Packaging', 'new'),

-- 16. Packaging – Coir Rolls (base: 50cm)
('Coir Packaging Rolls', 'Coir rolls for custom packaging. Available in 50cm, 70cm, 100cm widths, each 10m length.', 450.00, 60, 'coir-rolls.jpg', 'Packaging', 'trending'),

-- 17. Packaging – Coir Nets (base: Small)
('Coir Nets', 'Coir nets for wrapping and protection. Available in Small (50x50cm, pack of 5) and Large (100x100cm, pack of 3).', 220.00, 120, 'coir-nets.jpg', 'Packaging', 'best_seller'),

-- 18. Packaging – Coir Rope (base: 5mm)
('Coir Rope', 'Natural coir rope for tying packages. Available in 5mm x 50m, 8mm x 30m, 10mm x 20m.', 180.00, 150, 'coir-rope.jpg', 'Packaging', 'none'),

-- 19. Packaging – Coir Twine (base: 2mm)
('Coir Twine', 'Fine coir twine for gift wrapping and packaging. Available in 2mm, 3mm, 4mm, each 100m.', 120.00, 200, 'coir-twine.jpg', 'Packaging', 'trending'),

-- 20. Packaging – Coir Cushioning (base: 5kg)
('Coir Cushioning', 'Loose coir fiber for cushioning packages. Available in 5kg and 10kg bags.', 250.00, 80, 'coir-cushioning.jpg', 'Packaging', 'new');

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
-- 1. Coir Pots (product_id = 1)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(1, 'Size', '3 inch (Pack of 10)', 120.00, 200),
(1, 'Size', '4 inch (Pack of 8)', 150.00, 180),
(1, 'Size', '6 inch (Pack of 5)', 180.00, 150);

-- 2. Coir Grow Bags (product_id = 2)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(2, 'Size', 'Small (5kg)', 180.00, 150),
(2, 'Size', 'Medium (10kg)', 320.00, 100),
(2, 'Size', 'Large (15kg)', 450.00, 75);

-- 3. Coir Poles (product_id = 3)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(3, 'Length', '2ft', 250.00, 80),
(3, 'Length', '3ft', 350.00, 60),
(3, 'Length', '4ft', 450.00, 40);

-- 4. Coco Peat Blocks (product_id = 4)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(4, 'Weight', '650g', 95.00, 300),
(4, 'Weight', '5kg', 280.00, 200),
(4, 'Weight', '10kg', 520.00, 100);

-- 5. Coir Chips (product_id = 5)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(5, 'Size', '2L Bag', 120.00, 150),
(5, 'Size', '5L Bag', 250.00, 100);

-- 6. Coir Mulch (product_id = 6)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(6, 'Size', '10L Bag', 180.00, 120),
(6, 'Size', '20L Bag', 320.00, 80);

-- 7. Doormats (product_id = 7)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(7, 'Size', 'Small (40x60cm)', 350.00, 100),
(7, 'Size', 'Large (50x80cm)', 550.00, 75),
(7, 'Size', 'Round (45cm)', 400.00, 60);

-- 8. Brushes (product_id = 8)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(8, 'Type', 'Kitchen (Small)', 180.00, 150),
(8, 'Type', 'Bath (Medium)', 220.00, 120),
(8, 'Type', 'Floor (Large)', 380.00, 80);

-- 9. Scourers (product_id = 9)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(9, 'Pack', 'Pack of 3', 120.00, 200),
(9, 'Pack', 'Pack of 5', 180.00, 150);

-- 10. Baskets (product_id = 10)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(10, 'Size', 'Small (25cm)', 280.00, 60),
(10, 'Size', 'Large (35cm)', 450.00, 40);

-- 11. Wall Hangings (product_id = 11)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(11, 'Size', 'Small (40x60cm)', 650.00, 30),
(11, 'Size', 'Large (60x90cm)', 1200.00, 15);

-- 12. Coir Slabs (Agriculture) (product_id = 12) - NEW
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(12, 'Size', 'Single (100x20x5cm)', 180.00, 120),
(12, 'Size', '5 Pack', 750.00, 80),
(12, 'Size', '10 Pack', 1400.00, 50);

-- 13. Coir Discs (product_id = 13)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(13, 'Size', '8cm (Pack of 50)', 380.00, 150),
(13, 'Size', '10cm (Pack of 30)', 350.00, 120),
(13, 'Size', '12cm (Pack of 20)', 340.00, 100);

-- 14. Coir Fiber (product_id = 14)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(14, 'Weight', '1kg Raw', 150.00, 200),
(14, 'Weight', '5kg Bale', 650.00, 80),
(14, 'Weight', '10kg Bale', 1200.00, 50);

-- 15. Coir Sheets (product_id = 15)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(15, 'Size', '50x50cm (Pack of 10)', 280.00, 100),
(15, 'Size', '70x100cm (Pack of 5)', 350.00, 80);

-- 16. Coir Rolls (product_id = 16)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(16, 'Width', '50cm x 10m', 450.00, 60),
(16, 'Width', '70cm x 10m', 580.00, 50),
(16, 'Width', '100cm x 10m', 780.00, 30);

-- 17. Coir Nets (product_id = 17)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(17, 'Size', 'Small (50x50cm) Pack of 5', 220.00, 120),
(17, 'Size', 'Large (100x100cm) Pack of 3', 280.00, 80);

-- 18. Coir Rope (product_id = 18)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(18, 'Diameter', '5mm x 50m', 180.00, 150),
(18, 'Diameter', '8mm x 30m', 200.00, 120),
(18, 'Diameter', '10mm x 20m', 220.00, 100);

-- 19. Coir Twine (product_id = 19)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(19, 'Thickness', '2mm x 100m', 120.00, 200),
(19, 'Thickness', '3mm x 100m', 150.00, 180),
(19, 'Thickness', '4mm x 100m', 180.00, 150);

-- 20. Coir Cushioning (product_id = 20)
INSERT INTO product_variations (product_id, variation_name, variation_value, price, stock) VALUES
(20, 'Weight', '5kg Bag', 250.00, 80),
(20, 'Weight', '10kg Bag', 450.00, 60);

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
UPDATE users SET is_verified = 1, verified_at = NOW() WHERE email = 'admin@creatrix.com';

