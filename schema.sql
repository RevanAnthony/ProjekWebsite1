
-- MySQL schema for Golden Spice
CREATE DATABASE IF NOT EXISTS golden_spice CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE golden_spice;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NULL,
  name VARCHAR(200) NOT NULL,
  description TEXT,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  spicy_level TINYINT NULL,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS reasons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description VARCHAR(500) NOT NULL,
  icon VARCHAR(60) DEFAULT 'bi-trophy'
);

CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  skey VARCHAR(100) UNIQUE NOT NULL,
  svalue TEXT
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(160) NOT NULL,
  phone VARCHAR(60) NOT NULL,
  address TEXT NOT NULL,
  notes TEXT,
  total DECIMAL(12,2) NOT NULL,
  status ENUM('baru','diproses','selesai','batal') DEFAULT 'baru',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  price DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  email VARCHAR(160) NOT NULL,
  subject VARCHAR(200),
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default content
INSERT INTO categories (name) VALUES
('Sambal Bawang'), ('Sambal Ijo'), ('Saus Mentai'), ('Spicy');

INSERT INTO reasons (title, description, icon) VALUES
('Saus Juara Buatan Sendiri','Dari sambal geprek sampai mentai creamy, kami racik sendiri untuk rasa yang otentik.','bi-emoji-heart-eyes'),
('Kualitas Tanpa Kompromi','Bahan segar pilihan & proses higienis untuk rasa mantap setiap suap.','bi-fire'),
('Cepat dan Praktis','Pesan via web, siapkan cepat; cocok untuk makan siang atau bekal.','bi-lightning');

INSERT INTO settings (skey, svalue) VALUES
('hero_title','STANDAR EMAS RICE BOWL PEDAS!'),
('hero_sub','Nikmati perpaduan sempurna nasi hangat, ayam krispi, dan saus spesial yang dibuat khusus untuk para pecinta sensasi pedas.'),
('hero_cta','Lihat Menu & Pesan Sekarang');

-- Sample products with placeholder images
INSERT INTO products (category_id, name, description, price, spicy_level, image) VALUES
(1, 'Sambal Bawang', 'Ayam krispi geprek dengan sambal bawang khas Golden Spice yang pedas menggigit dan bikin nagih.', 22000, 4, 'sambal-bawang.jpg'),
(2, 'Sambal Ijo', 'Ayam krispi sambal ijo segar, paduan pedas gurih dengan aroma rempah menggoda.', 22000, 3, 'sambal-ijo.jpg'),
(3, 'Saus Mentai', 'Creamy saus mentai ala Jepang, gurih dan nikmat untuk kamu yang suka keju & krim.', 25000, 2, 'saus-mentai.jpg'),
(4, 'Spicy''C', 'Ayam crispy dengan saus super pedas; tantangan buat pecinta pedas!', 24000, 5, 'spicy.jpg');
