-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 27, 2026 at 11:37 AM
-- Server version: 10.6.24-MariaDB-cll-lve
-- PHP Version: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `6831eecaafc85_avtopilotminiapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `telegram_id` bigint(20) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bot_config`
--

CREATE TABLE `bot_config` (
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bot_states`
--

CREATE TABLE `bot_states` (
  `telegram_id` bigint(20) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `telegram_link` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `section_id`, `title`, `description`, `cover_image`, `telegram_link`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 9, '1- Dars miyani ochish', 'Mentor: Anvar Abduqayumov', 'img_69ec89060bf2a3.43223442.jpg', 'https://t.me/c/3729056281/98?thread=77', 1, 1, '2026-04-25 09:18:12', '2026-04-25 09:27:34'),
(3, 9, '2- Dars miyani ochish', 'Mentor: Anvar Abduqayumov', 'img_69ec8915a66f74.73835722.jpg', 'https://t.me/c/3729056281/99?thread=77', 2, 1, '2026-04-25 09:22:07', '2026-04-25 09:27:49'),
(4, 9, '3- Dars miyani ochish', 'Mentor: Anvar Abduqayumov', 'img_69ec885ab53e45.96152581.jpg', 'https://t.me/c/3729056281/100?thread=77', 3, 1, '2026-04-25 09:24:36', '2026-04-25 09:24:42'),
(5, 9, '4- Dars miyani ochish', 'Mentor: Anvar Abduqayumov', 'img_69ec888e5eb998.87616648.jpg', 'https://t.me/c/3729056281/101?thread=77', 4, 1, '2026-04-25 09:25:29', '2026-04-25 09:25:34'),
(6, 9, '5- Dars miyani ochish', 'Mentor: Anvar Abduqayumov', 'img_69ec88a754f141.14113531.jpg', 'https://t.me/c/3729056281/102?thread=77', 5, 1, '2026-04-25 09:25:53', '2026-04-25 09:25:59'),
(7, 9, '6- Dars miyani ochish', 'Mentor: Anvar Abduqayumov', 'img_69ec88dd033bd9.63456386.jpg', 'https://t.me/c/3729056281/103?thread=77', 6, 1, '2026-04-25 09:26:45', '2026-04-25 09:26:53'),
(8, 9, 'Reja tuzish (1- Qism)', 'Mentor: Anvar Abduqayumov', 'img_69ec8a3f633289.59546015.jpg', 'https://t.me/c/3729056281/104?thread=77', 7, 1, '2026-04-25 09:32:33', '2026-04-25 09:32:47'),
(9, 9, 'Reja tuzish (2- Qism)', 'Mentor: Anvar Abduqayumov', 'img_69ec8a8261d6d2.19627857.jpg', 'https://t.me/c/3729056281/105?thread=77', 8, 1, '2026-04-25 09:33:48', '2026-04-25 09:33:54'),
(10, 9, 'Biznes vision (1- Qism)', 'Mentor: Anvar Abduqayumov', 'img_69ec8ad2d72e38.74097646.jpg', 'https://t.me/c/3729056281/128?thread=77', 9, 1, '2026-04-25 09:35:05', '2026-04-25 09:35:59'),
(11, 9, 'Biznes visionni kengaytirish (2- Qism)', 'Mentor: Anvar Abduqayumov', 'img_69ec8b3279ec88.86811566.jpg', 'https://t.me/c/3729056281/129?thread=77', 10, 1, '2026-04-25 09:36:42', '2026-04-25 09:36:50'),
(12, 8, 'Korxonani raqamlar orqali boshqarish', 'Mentor: Anvar Abduqayumov', 'img_69ecba39635329.61200138.jpg', 'https://t.me/c/3729056281/130?thread=76', 1, 1, '2026-04-25 12:57:21', '2026-04-25 12:57:29'),
(13, 8, 'HR:Recruiting(xodimlarni yollash)', 'Mentor: Dilrabo Khoshimova', 'img_69ecbab6b049f1.88847567.jpg', 'https://t.me/c/3729056281/132?thread=76', 2, 1, '2026-04-25 12:59:28', '2026-04-25 12:59:34'),
(14, 8, 'Xodimlar uchun lavozim yo`riqnomalari', 'Mentor: Dilrabo Khoshimova', 'img_69ecbae5dfd919.28535479.jpg', 'https://t.me/c/3729056281/133?thread=76', 3, 1, '2026-04-25 13:00:14', '2026-04-25 13:00:22'),
(15, 8, 'Sizsiz ham ishlaydigan biznes model/ Orgstruktura', 'Mentor: Anvar Abduqayumov', 'img_69ecbb911e1b05.74880778.jpg', 'https://t.me/c/3729056281/429?thread=76', 4, 1, '2026-04-25 13:03:05', '2026-04-25 13:03:13'),
(16, 8, 'Biznesda Amartizatsiyani to`gri hisoblash', 'Mentor: Anvar Abduqayumov', 'img_69ecbbdef154e5.43517573.jpg', 'https://t.me/c/3729056281/430?thread=76', 5, 1, '2026-04-25 13:04:24', '2026-04-25 13:04:31'),
(17, 8, 'Biznesda ROI hisoblash', 'Mentor: Anvar Abduqayumov', 'img_69ecbcd67fc5d3.41303411.jpg', 'https://t.me/c/3729056281/431?thread=76', 6, 1, '2026-04-25 13:06:38', '2026-04-25 13:08:38'),
(18, 8, 'SWOT analizi', 'Mentor: Anvar Abduqayumov', 'img_69ecbd11307e06.80035523.jpg', 'https://t.me/c/3729056281/134?thread=75', 7, 0, '2026-04-25 13:09:30', '2026-04-25 13:16:58'),
(19, 7, 'SWOT analizi', 'Mentor: Anvar Abduqayumov', 'img_69ecbf15eef211.25806906.jpg', 'https://t.me/c/3729056281/134?thread=75', 1, 1, '2026-04-25 13:18:06', '2026-04-25 13:18:14'),
(20, 7, 'Custdev- Customer development', 'Mentor: Anvar Abduqayumov', 'img_69ecbf71449392.93857443.jpg', 'https://t.me/c/3729056281/135?thread=75', 2, 1, '2026-04-25 13:19:38', '2026-04-25 13:19:45'),
(21, 7, 'Raqobatchilar analizi', 'Mentor: Anvar Abduqayumov', 'img_69ecbf94af1557.93972647.jpg', 'https://t.me/c/3729056281/414?thread=75', 3, 1, '2026-04-25 13:20:13', '2026-04-25 13:20:20'),
(22, 7, 'Bozor analizi', 'Mentor: Anvar Abduqayumov', 'img_69ecbfcb91ff06.90389478.jpg', 'https://t.me/c/3729056281/416?thread=75', 4, 1, '2026-04-25 13:21:09', '2026-04-25 13:21:15'),
(23, 7, 'SMM, Telegram, Youtube orqali mijoz oqimini yaratish', 'Mentor: Anvar Abduqayumov', 'img_69ecc01b1b2ca8.03241166.jpg', 'https://t.me/c/3729056281/432?thread=75', 5, 1, '2026-04-25 13:22:23', '2026-04-25 13:22:35'),
(24, 7, 'Vistavkalar va delegatsiyalar orqali mijoz topish', 'Mentor: Anvar Abduqayumov', 'img_69ecc03e9e9336.63239717.jpg', 'https://t.me/c/3729056281/433?thread=75', 6, 1, '2026-04-25 13:23:05', '2026-04-25 13:23:10'),
(25, 7, 'Mijozlarning og`riqli nuqtalarini topish', 'Mentor: Anvar Abduqayumov', 'img_69ecc07daec2d3.47114125.jpg', 'https://t.me/c/3729056281/434?thread=75', 7, 1, '2026-04-25 13:24:08', '2026-04-25 13:24:13'),
(26, 7, 'Mijozlar bazasi', 'Mentor: Anvar Abduqayumov', 'img_69ecc0a587f5a3.09224491.jpg', 'https://t.me/c/3729056281/435?thread=75', 8, 1, '2026-04-25 13:24:49', '2026-04-25 13:24:53'),
(27, 6, 'Upsell/Crossell/Downsell- sotuvni oshiruvchi samarali usullar', 'Mentor: Anvar Abduqayumov', 'img_69ecc2b8a972d3.83665235.jpg', 'https://t.me/c/3729056281/436?thread=78', 1, 1, '2026-04-25 13:33:39', '2026-04-25 13:33:44'),
(28, 6, 'Sotuvda sotuv skriptini qanday tuzish kerak?', 'Mentor: Anvar Abduqayumov', 'img_69ecc2fe460145.07316122.jpg', 'https://t.me/c/3729056281/437?thread=78', 2, 1, '2026-04-25 13:34:48', '2026-04-25 13:34:54'),
(29, 5, 'Biznesda o’sish bosqichlari (1-qism)', 'Mentor: Anvar Abduqayumov', 'img_69ecc36068c2f8.36311879.jpg', 'https://t.me/c/3729056281/71?thread=8', 1, 1, '2026-04-25 13:36:27', '2026-04-25 13:36:32'),
(30, 5, 'Biznesda qadamma-qadam o`sish', 'Mentor: Anvar Abduqayumov', 'img_69ecc3a59b78f5.32951940.jpg', 'https://t.me/c/3729056281/73?thread=8', 2, 1, '2026-04-25 13:37:28', '2026-04-25 13:37:41'),
(31, 5, 'Razbor  va savol-javob jonli efiri (16.04)', 'Mentor: Anvar Abduqayumov', 'img_69ecc3c8b9b833.45934406.jpg', 'https://t.me/c/3729056281/747?thread=8', 3, 1, '2026-04-25 13:38:09', '2026-04-25 13:38:16'),
(32, 2, 'Maqsad qo`yish', 'Mentor: Anvar Abduqayumov', 'img_69ecc44e5a3919.37669938.jpg', 'https://t.me/c/3729056281/125?thread=5', 1, 0, '2026-04-25 13:40:20', '2026-04-25 16:33:49'),
(33, 2, 'Maqsad qo`yish 2-qism', 'Mentor: Anvar Abduqayumov', 'img_69ecc47b756ed4.08681487.jpg', 'https://t.me/c/3729056281/126?thread=5', 2, 0, '2026-04-25 13:41:10', '2026-04-25 16:34:06'),
(34, 2, 'Dangasalikni yengish', 'Mentor: Anvar Abduqayumov', 'img_69ecc496e052c9.38608725.jpg', 'https://t.me/c/3729056281/127?thread=5', 3, 0, '2026-04-25 13:41:38', '2026-04-25 16:33:59'),
(35, 1, '(1-dars) “Biznes psixologiyasi”', 'Mentor: Anvar Abduqayumov', 'img_69ecc61456f643.68145327.jpg', 'https://t.me/c/3729056281/51?thread=4', 1, 1, '2026-04-25 13:47:59', '2026-04-25 13:48:04'),
(36, 1, '(2-dars) G’oya topish va saralash', 'Mentor: Anvar Abduqayumov', 'img_69ecc63f3f4674.50016942.jpg', 'https://t.me/c/3729056281/60?thread=4', 2, 1, '2026-04-25 13:48:39', '2026-04-25 13:48:47'),
(37, 1, 'Biznesning 4ta qonuni', 'Mentor: Anvar Abduqayumov', 'img_69ecc667cb5d47.75435609.jpg', 'https://t.me/c/3729056281/61?thread=4', 3, 1, '2026-04-25 13:49:23', '2026-04-25 13:49:27'),
(38, 1, 'Lokatsiya ichki omillar. Arendator bilan munosabat', 'Mentor: Anvar Abduqayumov', 'img_69ecc69aa6bdb7.81490175.jpg', 'https://t.me/c/3729056281/63?thread=4', 4, 1, '2026-04-25 13:49:58', '2026-04-25 13:50:18'),
(39, 1, 'Buxgalteriya, omborxona va qo’riqlash tizimini yaratish', 'Mentor: Anvar Abduqayumov', 'img_69ecc6c56b8971.75193864.jpg', 'https://t.me/c/3729056281/66?thread=4', 5, 1, '2026-04-25 13:50:55', '2026-04-25 13:51:01'),
(40, 1, 'Kadrlarni tashkil qilish, uyishtirish, boshqarish', 'Mentor: Anvar Abduqayumov', 'img_69ecc6e606d603.80766560.jpg', 'https://t.me/c/3729056281/67?thread=4', 6, 1, '2026-04-25 13:51:26', '2026-04-25 13:51:34'),
(41, 1, 'Operatsiyon ishlarni rejalashtirish', 'Mentor: Anvar Abduqayumov', 'img_69ecc71b34e272.60170359.jpg', 'https://t.me/c/3729056281/68?thread=4', 7, 1, '2026-04-25 13:52:22', '2026-04-25 13:52:27'),
(42, 1, 'Brend va logotip', 'Mentor: Anvar Abduqayumov', 'img_69ecc73dcb5948.97969329.jpg', 'https://t.me/c/3729056281/70?thread=4', 8, 1, '2026-04-25 13:52:56', '2026-04-25 13:53:01'),
(43, 2, 'Guruh tanishtiruvi(1-qism)', 'Mentor: Anvar Abduqayumov', 'img_69ed105e5746a0.21597830.jpg', 'https://t.me/c/3729056281/48?thread=10', 4, 1, '2026-04-25 19:04:56', '2026-04-25 19:05:03'),
(44, 2, 'Guruh tanishtiruvi (2-qism)', 'Mentor: Anvar Abduqayumov', 'img_69ed110ac5cc29.04088554.jpg', 'https://t.me/c/3729056281/49?thread=10', 5, 1, '2026-04-25 19:07:47', '2026-04-25 19:07:55'),
(45, 2, 'Autopilot loyihasining qadriyatlari', 'Mentor: Anvar Abduqayumov', 'img_69ed11e50dc007.68328559.jpg', 'https://t.me/c/3729056281/50?thread=10', 6, 1, '2026-04-25 19:11:25', '2026-04-25 19:11:33'),
(46, 10, 'Maqsad qo`yish', 'Mentor: Anvar Abduqayumov', 'img_69ed131ca10c23.33952260.jpg', 'https://t.me/c/3729056281/125?thread=5', 1, 1, '2026-04-25 19:16:39', '2026-04-25 19:16:44'),
(47, 10, 'Maqsad qo`yish 2-qism', 'Mentor: Anvar Abduqayumov', 'img_69ed13499c7392.10032201.jpg', 'https://t.me/c/3729056281/126?thread=5', 2, 1, '2026-04-25 19:17:24', '2026-04-25 19:17:29'),
(48, 10, 'Dangasalikni yengish', 'Mentor: Anvar Abduqayumov', 'img_69ed1367a5a4a7.30676496.jpg', 'https://t.me/c/3729056281/127?thread=5', 3, 1, '2026-04-25 19:17:53', '2026-04-25 19:17:59'),
(49, 3, 'Yeryong’oqpastasi ideasi bo`yicha qisqacha tahlil', 'Mentor: Anvar Abduqayumov', 'img_69ee199f2d87e0.87557702.jpg', 'https://t.me/c/3729056281/441?thread=6', 1, 1, '2026-04-26 13:56:41', '2026-04-26 13:56:47'),
(50, 3, 'Eng mashhur bo’lgan sport turlaridan ham har xil turdagi suvenirlar ishlab chiqishadi, har xil detallar…….', 'Mentor: Anvar Abduqayumov', 'img_69ee26ec0843f9.19588437.jpg', 'https://t.me/c/3729056281/87?thread=6', 2, 1, '2026-04-26 14:53:11', '2026-04-26 14:53:32'),
(51, 3, 'Kafelar konsepsiyasi \n\nTabiat qo’ynida o’tirgandek bo’lasiz🌿', 'Mentor: Anvar Abduqayumov', 'img_69ee2745ec8750.20225147.jpg', 'https://t.me/c/3729056281/88?thread=6', 3, 1, '2026-04-26 14:54:57', '2026-04-26 14:55:02'),
(52, 3, 'Shlyapachi do’koni ideasi', 'Mentor: Anvar Abduqayumov', 'img_69ee277ca49a28.04183030.jpg', 'https://t.me/c/3729056281/90?thread=6', 4, 1, '2026-04-26 14:55:51', '2026-04-26 14:55:56'),
(53, 3, 'Na’matakni turklarda “kushburnu” deb ataydi \n\nO’zimizda ham na’matakni o’zini sotish bilan birga na’matak murabbosini tayyorlab, sotsak bo’ladi', 'Mentor: Anvar Abduqayumov', 'img_69ee27a1a35c40.35813662.jpg', 'https://t.me/c/3729056281/91?thread=6', 5, 1, '2026-04-26 14:56:25', '2026-04-26 14:56:33'),
(54, 3, 'Leon-tabiiy fast food tushunchasi ideasi\n\nBreakfast-lunch-dinner', 'Mentor: Anvar Abduqayumov', 'img_69ee27c2bddf49.89751208.jpg', 'https://t.me/c/3729056281/92?thread=6', 6, 1, '2026-04-26 14:57:01', '2026-04-26 14:57:06'),
(55, 3, 'Oddiy produktlarni ham chiroyli upakovka qilib sotishadi🔥', 'Mentor: Anvar Abduqayumov', 'img_69ee27e6e25825.97545542.jpg', 'https://t.me/c/3729056281/94?thread=6', 7, 1, '2026-04-26 14:57:36', '2026-04-26 14:57:43'),
(56, 3, 'Har xil assortimentlar bilan do’konni bezatishni yorvorishadi. \n\nDo’konlarda hattoki anjir sirkasi ham bor…', 'Mentor: Anvar Abduqayumov', 'img_69ee280acff8c2.86771792.jpg', 'https://t.me/c/3729056281/95?thread=6', 8, 1, '2026-04-26 14:58:09', '2026-04-26 14:58:18'),
(57, 3, 'Kichik-kichik magazinlarda million xil sirlar va zaytunlar sotuvi ideasi', 'Mentor: Anvar Abduqayumov', 'img_69ee282c1f2e74.49362502.jpg', 'https://t.me/c/3729056281/96?thread=6', 9, 1, '2026-04-26 14:58:46', '2026-04-26 14:58:52'),
(58, 3, 'Yevropada zo’r pul keltiradigan “tozalik xizmati” biznesi', 'Mentor: Anvar Abduqayumov', 'img_69ee284b50db92.46234511.jpg', 'https://t.me/c/3729056281/97?thread=6', 10, 1, '2026-04-26 14:59:17', '2026-04-26 14:59:23'),
(59, 3, 'Restoranlarni o’ravolish uchun eng zo’r yechim bo’ladigan usul', 'Mentor: Anvar Abduqayumov', 'img_69ee286e5dbf16.28710058.jpg', 'https://t.me/c/3729056281/108?thread=6', 11, 1, '2026-04-26 14:59:52', '2026-04-26 14:59:58'),
(60, 3, 'Choylarni suyuqlikka aylantirib, sotuvga chiqarish \n\nMasalan: imbir misolida videoda ko’rsatdim', 'Mentor: Anvar Abduqayumov', 'img_69ee28995d61d4.54359410.jpg', 'https://t.me/c/3729056281/109?thread=6', 12, 1, '2026-04-26 15:00:23', '2026-04-26 15:00:41'),
(61, 3, 'Yer yong’oq pastasi biznesi', 'Mentor: Anvar Abduqayumov', 'img_69ee28b315bf61.98552543.jpg', 'https://t.me/c/3729056281/110?thread=6', 13, 1, '2026-04-26 15:01:01', '2026-04-26 15:01:07'),
(62, 3, 'Yuqorida “reklama taxtachasi”biznesi haqida aytib o’tgan edim, shuni davomini, ideani kengaytirib bermoqchiman', 'Mentor: Anvar Abduqayumov', 'img_69ee28d292c327.62763795.jpg', 'https://t.me/c/3729056281/111?thread=6', 14, 1, '2026-04-26 15:01:32', '2026-04-26 15:01:38'),
(63, 3, 'Hozirda mashhur bo’layotgan “reklama taxtachasi” biznesi', 'Mentor: Anvar Abduqayumov', 'img_69ee28f4b6c1d1.38375730.jpg', 'https://t.me/c/3729056281/113?thread=6', 15, 1, '2026-04-26 15:02:05', '2026-04-26 15:02:12'),
(64, 3, 'Mangoliyada zo’r rivojlanayotgan go’sht biznesi', 'Mentor: Anvar Abduqayumov', 'img_69ee29163aed05.20721396.jpg', 'https://t.me/c/3729056281/114?thread=6', 16, 1, '2026-04-26 15:02:39', '2026-04-26 15:02:46'),
(65, 3, 'Ko’chada ishlatiladigan gigant zontiklar ishlab chiqarishni yo’lga qo’yish', 'Mentor: Anvar Abduqayumov', 'img_69ee293743e5b6.38073585.jpg', 'https://t.me/c/3729056281/115?thread=6', 17, 1, '2026-04-26 15:03:14', '2026-04-26 15:03:19'),
(66, 3, 'Har bitta viloyatning o’zining milliy taomi bor, shu milliy taom bilan yuritilayotgan restoran ochish', 'Mentor: Anvar Abduqayumov', 'img_69ee29644e6844.51765605.jpg', 'https://t.me/c/3729056281/116?thread=6', 18, 1, '2026-04-26 15:03:59', '2026-04-26 15:04:04'),
(67, 3, 'Italiyada mashhur bo’lgan “outdoor chair” biznesi', 'Mentor: Anvar Abduqayumov', 'img_69ee29866cf241.13115912.jpg', 'https://t.me/c/3729056281/117?thread=6', 19, 1, '2026-04-26 15:04:32', '2026-04-26 15:04:38'),
(68, 3, 'Tog’li hududlarda, manzarali joylarga mos keladigan biznes idea bilan ustoz bo’lishdilar, siz ham eshitib, fikrlaringizni qoldiring', 'Mentor: Anvar Abduqayumov', 'img_69ee2a242156c9.96387525.jpg', 'https://t.me/c/3729056281/118?thread=6', 20, 1, '2026-04-26 15:06:35', '2026-04-26 15:07:16'),
(69, 3, 'Muzlagan holatdagi mahsulotlarni sotuvini yo’lga qo’yish', 'Mentor: Anvar Abduqayumov', 'img_69ee2a5181ca69.13340780.jpg', 'https://t.me/c/3729056281/179?thread=6', 21, 1, '2026-04-26 15:07:55', '2026-04-26 15:08:01'),
(70, 3, 'Yumshoq va yog’och o’yinchoqlarni ishlab chiqarish va sotuvini yo’lsa qo’ysa, zo’r ketadi…', 'Mentor: Anvar Abduqayumov', 'img_69ee2a749fafb0.46250992.jpg', 'https://t.me/c/3729056281/359?thread=6', 22, 1, '2026-04-26 15:08:29', '2026-04-26 15:08:36'),
(71, 4, 'Biznesdagi muammolarni o’z vaqtida yechim qilish bo’yicha keyslarim bilan bo’lishdim.', 'Mentor: Anvar Abduqayumov', 'img_69ee2ac14feca8.17763695.jpg', 'https://t.me/c/3729056281/120?thread=7', 1, 1, '2026-04-26 15:09:48', '2026-04-26 15:09:53'),
(72, 4, 'Har qanday yuksalish tasavvurdan boshlanadi.\nInson boy yoki mashhur bo‘lishi uchun avval o‘sha darajadagi hayotni ko‘rishi, his qilishi va shunga intilishi kerak.\n\nSiz ham fikrimga qo’shilasizmi?', 'Mentor: Anvar Abduqayumov', 'img_69ee2aee7ad0b3.83960937.jpg', 'https://t.me/c/3729056281/121?thread=7', 2, 1, '2026-04-26 15:10:32', '2026-04-26 15:10:38'),
(73, 4, 'Kundalik tutish bo’yicha ustoz o’z kundaliklari bilan bo’lishdilar.', 'Mentor: Anvar Abduqayumov', 'img_69ee2b1b59f0e9.66641119.jpg', 'https://t.me/c/3729056281/122?thread=7', 3, 1, '2026-04-26 15:11:05', '2026-04-26 15:11:23'),
(74, 4, 'Muvaffaqiyatli odamning har kuni aniq rejasi bo‘ladi. Reja yo‘q joyda-natija ham bo‘lmaydi', 'Mentor: Anvar Abduqayumov', 'img_69ee2b3a456d69.27373137.jpg', 'https://t.me/c/3729056281/123?thread=7', 4, 1, '2026-04-26 15:11:44', '2026-04-26 15:11:54'),
(75, 4, 'O’zimni eng kuchli raqibimni qanday qilib bozordan chiqazib yuborganman…', 'Mentor: Anvar Abduqayumov', 'img_69ee2b61cd9ed9.04185044.jpg', 'https://t.me/c/3729056281/124?thread=7', 5, 1, '2026-04-26 15:12:28', '2026-04-26 15:12:33'),
(76, 4, '“Vaqtni boshqarishni qanday o’zlashtirish” haqida qisqacha qo’llanma', 'Mentor: Anvar Abduqayumov', 'img_69ee2bfe8dd741.94535263.jpg', 'https://t.me/c/3729056281/119', 6, 1, '2026-04-26 15:15:06', '2026-04-26 15:15:10'),
(77, 13, 'Test darsliklar', 'Test uchun qoʻshilgan darsliklar tavsifi', 'img_69ee373777eec4.17766095.jpg', 'https://t.me/test123', 1, 1, '2026-04-26 16:02:32', '2026-04-26 16:03:03');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `image_path`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Anvar Abduqayumov yangi biznes strategiyasi', 'Anvar Abduqayum Yoʻldoshev oʻzbekistonlik futbolchi Abduqodir Husanov va alaykum assalom aleykum hurmatli va alaykum assalom aleykum', 'img_69eeefa43c5f32.24209314.jpg', 1, 0, '2026-04-27 05:08:00', '2026-04-27 06:33:29'),
(2, 'Oʻzbekistondagi eng boy tadbirkor', 'Asaka tumani tarkibidagi qishloq uyi boʻlsin deb yoz oylarida gullab mevasi iyulda pishib yetiladi va alaykum assalom aleykum hurmatli va alaykum assalom aleykum hurmatli va alaykum assalom aleykum hurmatli va alaykum assalom aleykum hurmatli va aziz do\'stlarim va alaykum assalom aleykum hurmatli', 'img_69eeeff50ef4f4.22658815.jpg', 2, 0, '2026-04-27 05:11:10', '2026-04-27 06:33:24'),
(3, 'ToyBolani qurish jarayoni', 'Toʻy va boshqa videoni tomosha qiling va siz ham shunday qilib siz uchun havolalar va u kursda va boshqa tadbirkorlar bilan', 'img_69eef018ab5700.14104917.jpg', 3, 0, '2026-04-27 05:11:46', '2026-04-27 06:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_path` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `image_path`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Shogirdlik kursi', 'img_69ed0f57dbdfc5.79231284.jpg', 1, 1, '2026-04-24 18:10:12', '2026-04-25 19:00:39'),
(2, 'Yangi aʼzolar', 'img_69ed036a79a200.57312781.jpg', 2, 1, '2026-04-24 18:10:12', '2026-04-25 18:09:46'),
(3, 'Biznes gʻoyalar', 'img_69ec7d8ba9e3b3.05739800.jpg', 3, 1, '2026-04-24 18:10:12', '2026-04-25 16:10:21'),
(4, 'Kunlik insaydlar', 'img_69ec7e119ef9d5.42210609.jpg', 4, 1, '2026-04-24 18:10:12', '2026-04-25 08:40:49'),
(5, 'Jonli efirlar', 'img_69ec7e61f00793.65621623.jpg', 5, 1, '2026-04-24 18:10:12', '2026-04-25 08:42:10'),
(6, 'Avtopilot sotuv', 'img_69ec7f6ab90315.85425771.jpg', 6, 1, '2026-04-24 18:10:12', '2026-04-25 08:46:34'),
(7, 'Avtopilot marketing', 'img_69ec82fb37c049.37448553.jpg', 7, 1, '2026-04-24 18:10:12', '2026-04-25 09:01:47'),
(8, 'Avtopilot tizim', 'img_69ec80c822e428.24710392.jpg', 8, 1, '2026-04-24 18:10:12', '2026-04-25 08:52:24'),
(9, 'Avtopilot miya', 'img_69ec813ac7a412.08964468.jpg', 9, 1, '2026-04-24 18:10:12', '2026-04-25 08:54:19'),
(10, 'Tafakkur darslari', 'img_69eced85b6ceb4.08385771.jpg', 10, 1, '2026-04-24 18:10:12', '2026-04-25 16:36:21'),
(12, 'Way', 'img_69ee118154b6c5.83727101.jpg', 11, 0, '2026-04-26 13:22:01', '2026-04-26 13:22:46'),
(13, 'Test', 'img_69ee36c2abe594.50672627.jpg', 12, 0, '2026-04-26 16:00:54', '2026-04-26 16:23:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `telegram_id` bigint(20) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `notifications` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `telegram_id`, `username`, `full_name`, `notifications`, `created_at`, `updated_at`) VALUES
(1, 8401977443, 'aijumaev', 'Jumaev - AI Expert', 1, '2026-04-25 04:36:40', '2026-04-27 06:33:30'),
(3, 7843449680, 'Vip_486', '𝗩𝗜𝗣 - 𝗣𝗿𝗲𝗺𝗶𝘂𝗺', 0, '2026-04-25 04:38:42', '2026-04-27 05:07:44'),
(20, 7827538214, 'OLDmenejer', '𝗢𝗟𝗗 - 𝗠𝗲𝗻𝗲𝗷𝗲𝗿™', 0, '2026-04-25 04:43:30', '2026-04-27 05:07:18'),
(140, 7932275988, 'jakhon_shaxsiy', 'Jakhongir', 0, '2026-04-25 05:45:21', '2026-04-26 09:08:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_id` (`telegram_id`);

--
-- Indexes for table `bot_config`
--
ALTER TABLE `bot_config`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `bot_states`
--
ALTER TABLE `bot_states`
  ADD PRIMARY KEY (`telegram_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id` (`section_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_id` (`telegram_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=707;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
