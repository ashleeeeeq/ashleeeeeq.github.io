-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 03:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coffeeshop_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `price_tipid_shot` decimal(10,2) DEFAULT NULL,
  `price_tamang_tama` decimal(10,2) DEFAULT NULL,
  `price_todo_busog` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `product_name`, `price`, `image`, `created_at`, `price_tipid_shot`, `price_tamang_tama`, `price_todo_busog`) VALUES
(1, 'Caramel Macchiato', 180.00, 'https://images.unsplash.com/photo-1579888071069-c107a6f79d82?auto=format&fit=crop&q=80&w=870', '2025-11-08 22:42:04', 120.00, 180.00, 210.00),
(2, 'Iced Spanish Latte', 165.00, 'https://images.unsplash.com/photo-1658646479124-bc31e6849497?auto=format&fit=crop&q=80&w=951', '2025-11-08 22:42:04', 110.00, 165.00, 195.00),
(3, 'Mocha Espresso', 150.00, 'https://media.istockphoto.com/id/2220401154/photo/coffee-cup-and-coffee-beans-on-wooden-table-espresso-crema-coffee-cup-aromatic.jpg?s=612x612&w=0&k=20&c=M71IHjkETXUNqz-Lda5kSIg1KVmpK_XFbf-GvIg_kIQ=', '2025-11-08 22:42:04', 100.00, 150.00, 180.00),
(4, 'Classic Cappuccino', 140.00, 'https://media.istockphoto.com/id/1250721187/photo/cup-of-cappuccino-coffee-with-sugar-on-a-marble-table.jpg?s=612x612&w=0&k=20&c=dW_mIwvmx1fz6OV3KdGMIA-M_t_N6vzmLti0p-eqNBs=', '2025-11-08 22:42:04', 95.00, 140.00, 165.00),
(5, 'Salted Caramel', 175.00, 'https://plus.unsplash.com/premium_photo-1723759448747-1d174225e61f?auto=format&fit=crop&q=80&w=870', '2025-11-08 22:42:04', 115.00, 175.00, 205.00),
(6, 'Hazelnut Latte', 170.00, 'https://media.istockphoto.com/id/1247810487/photo/diverse-keto-dishes.webp?a=1&b=1&s=612x612&w=0&k=20&c=9tfrfXIe2NMPaith4AixvMHhVI-cPKHMaESh9ZcJ1-c=', '2025-11-08 22:42:04', 110.00, 170.00, 200.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size` varchar(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `menu_id`, `product_name`, `size`, `quantity`, `price`, `total`, `status`, `created_at`, `updated_at`) VALUES
(8, 6, 5, 'Salted Caramel', 'todo_busog', 2, 205.00, 410.00, 'Completed', '2025-11-16 16:07:04', '2025-11-16 16:56:34'),
(9, 6, 6, 'Hazelnut Latte', 'tamang_tama', 1, 170.00, 170.00, 'Completed', '2025-11-16 16:26:39', '2025-11-16 16:56:41'),
(10, 2, 4, 'Classic Cappuccino', 'tipid_shot', 3, 95.00, 285.00, 'Processing', '2025-11-16 16:30:50', '2025-11-16 16:56:47'),
(11, 3, 1, 'Caramel Macchiato', 'tamang_tama', 2, 180.00, 360.00, 'Completed', '2025-11-16 16:32:38', '2025-11-16 16:56:56'),
(12, 3, 3, 'Mocha Espresso', 'tipid_shot', 1, 100.00, 100.00, 'Pending', '2025-11-16 16:32:47', '2025-11-16 16:32:47'),
(13, 5, 6, 'Hazelnut Latte', 'tipid_shot', 1, 110.00, 110.00, 'Processing', '2025-11-16 16:37:26', '2025-11-16 16:39:08'),
(14, 5, 2, 'Iced Spanish Latte', 'tamang_tama', 1, 165.00, 165.00, 'Pending', '2025-11-16 16:37:48', '2025-11-16 16:37:48'),
(15, 19, 5, 'Salted Caramel', 'tamang_tama', 1, 175.00, 175.00, 'Processing', '2025-11-17 13:05:33', '2025-11-17 13:36:33'),
(16, 19, 6, 'Hazelnut Latte', 'tipid_shot', 2, 110.00, 220.00, 'Pending', '2025-11-17 13:05:47', '2025-11-17 13:05:47'),
(17, 19, 1, 'Caramel Macchiato', 'todo_busog', 1, 210.00, 210.00, 'Completed', '2025-11-17 13:06:05', '2025-11-17 13:36:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `plain_password` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `plain_password`, `profile_pic`) VALUES
(1, 'Admin Coffee', 'admin@coffee.com', '$2y$10$URp8RGX0vxP8B0N4v9eui.5Dyi0vu2DG4LwSKnhxiKpD4hqZqGtua', 'admin', '2025-11-08 22:43:42', '2025-11-17 08:26:26', 'Admin123!', '1763367640_217ccd523f5594ab14f8.jpg'),
(2, 'Sample User', 'a@gmail.com', '$2y$10$b7JhitynZm/AsJdic5Ay2eU9hjH0P17WSkiWXdhgHmdhetnbHes2O', 'user', '2025-11-08 22:44:50', '2025-11-17 08:28:41', 'Sample123!', '1763368110_04e969e8f7c6e2d7ad5c.png'),
(3, 'Yagi', 'yaginaur@gmail.com', '$2y$10$KU8riOScdbXoFUuKMA8DleV4gNBc13x/A5Hggs7XrrnVkksVQxR7a', 'user', '2025-11-09 13:49:48', '2025-11-09 13:49:48', 'Kaizou0304!', NULL),
(4, 'EJ Liu', 'ozceane@gmail.com', '$2y$10$LJPokJ/Lh3Uv88FWLRc2Cu98IqDr9ZsMGJte39PX1/ACxI/Sbj2vi', 'admin', '2025-11-09 13:52:30', '2025-11-10 06:01:29', 'ElizahJoachim_04!', NULL),
(5, 'Kyeji Nakazawa', 'kyenakazawa@gmail.com', '$2y$10$bGEzIs.o8b6mreZ8oxnQm.aO95R6hOYm5A/wmtC6Q8gKsIFMswwbq', 'user', '2025-11-09 14:02:36', '2025-11-09 14:02:36', 'b0sS M4P4Gm4h4l !', NULL),
(6, 'Eris Conganco', 'erieri@gmail.com', '$2y$10$AcDaGxhClN0ULWH91aUgwO.jl36Y/IFUCL0a/8CCbT/4aeb/ZQufe', 'user', '2025-11-09 14:05:35', '2025-11-09 14:05:35', 'cutesy_Eri123', NULL),
(18, 'Keaton Jayde', 'beejolii900@gmail.com', '$2y$10$oAJgyvDhmiQb/mAYP.9xgO81Uuc./u6JEy3UsuZ/YB1xWjT8NbVcK', 'user', '2025-11-16 21:48:41', '2025-11-16 21:48:41', 'TryEmail123!', NULL),
(19, 'Janna Ashley', 'ashleeeeeq@gmail.com', '$2y$10$iOJ79cx9o3pZ01X1XKtCbu.PF6RHOqD/Ej1TDVg59lSsVUReb1pWK', 'user', '2025-11-17 19:22:27', '2025-11-17 12:16:33', '!ashleyJanna04', '1763381792_7b08416a66bc7fe6528d.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
