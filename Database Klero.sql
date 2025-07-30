-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2025 at 02:39 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wisata_klero`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`) VALUES
(4, 'Admin_1', '$2y$10$BlEulbItZeBciqgXbHU9aurWcgHAkUVt.toYY9gDnFOzvhCNclU8i', '');

-- --------------------------------------------------------

--
-- Table structure for table `umkm`
--

CREATE TABLE `umkm` (
  `id_umkm` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `umkm`
--

INSERT INTO `umkm` (`id_umkm`, `nama`, `deskripsi`, `gambar`, `created_at`) VALUES
(15, 'Berkah Madu & Royal Jelly', 'Alami manfaat luar biasa dari madu murni dan royal jelly dalam satu botol!\r\nBerkah Madu & Royal Jelly hadir sebagai solusi alami untuk menjaga daya tahan tubuh, meningkatkan energi, dan membantu menjaga kesehatan kulit dari dalam. Dipanen langsung dari peternakan lebah pilihan, tanpa campuran bahan kimia. Cocok untuk segala usia dan bisa dikonsumsi setiap hari!', 'download (11).jpeg', '2025-07-01 11:32:33'),
(16, 'Seblak Jeletet Klero', 'Rasakan pedasnya seblak khas Bandung dengan cita rasa yang meledak di mulut! Seblak Jeletet Klero hadir dengan kuah kental gurih dan tingkat kepedasan yang bisa disesuaikan. Isian lengkap mulai dari kerupuk basah, ceker, bakso, sosis, makaroni, hingga topping kekinian lainnya. Cocok untuk pecinta pedas sejati yang ingin menikmati sensasi seblak jeletet otentik tanpa harus jauh-jauh ke kota!\r\n', 'WhatsApp Image 2025-07-02 at 10.44.19_58dd1d07.jpg', '2025-07-01 11:33:01'),
(17, 'Seblak96 & Sop Duren', 'Kami menjual seblak dengan cita rasa khas, pedas, dan gurih yang bikin nagih! Tersedia berbagai pilihan topping seperti ceker, bakso, sosis, dan kerupuk yang kenyal. Cocok untuk pecinta makanan pedas yang ingin menikmati sensasi kuah kental dan bumbu rempah yang kuat. Selain itu kami juga menjual Sop Durian yang segar, manis dan legit dari durian asli, sangat cocok dipadukan dengan seblak. Pesan sekarang dan rasakan kelezatannya!', 'WhatsApp Image 2025-07-02 at 10.42.10_91b24032.jpg', '2025-07-02 02:50:56'),
(18, 'Ayam Bakar dan Nasi Goreng Klero', 'Nikmati ayam bakar dengan bumbu meresap dan nasi goreng khas yang gurih, dibuat dari bahan berkualitas dan cita rasa istimewa. Cocok untuk makan bersama keluarga atau teman. Harga terjangkau, rasa juara!', 'WhatsApp Image 2025-07-02 at 10.42.09_9eef9de3.jpg', '2025-07-02 02:51:52'),
(19, 'Warung Kana', 'tempat makan sederhana di Klero yang menyajikan berbagai masakan rumahan dengan cita rasa lezat dan harga terjangkau. Cocok untuk santap harian, dengan suasana nyaman dan pelayanan yang ramah. Menu bervariasi mulai dari ayam geprek, lele goreng, sayur lodeh, hingga minuman segar.', 'WhatsApp Image 2025-07-02 at 10.52.18_018119ca.jpg', '2025-07-02 02:53:33');

-- --------------------------------------------------------

--
-- Table structure for table `wisata`
--

CREATE TABLE `wisata` (
  `id_wisata` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `peta_iframe` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wisata`
--

INSERT INTO `wisata` (`id_wisata`, `nama`, `deskripsi`, `gambar`, `created_at`, `peta_iframe`) VALUES
(1, 'Sendang', 'Umbul Sendang Klero adalah sebuah sumber mata air alami yang jernih dan menyegarkan, terletak di Desa Klero, Kecamatan Tengaran, Kabupaten Semarang. Dikelilingi oleh pepohonan hijau dan suasana pedesaan yang tenang, Umbul ini menjadi tempat favorit warga lokal untuk mandi, bermain air, atau sekadar bersantai menikmati udara segar. Airnya berasal dari sumber alami pegunungan, menjadikan Umbul Sendang Klero sebagai destinasi yang cocok untuk melepas penat dan menikmati keindahan alam. Lokasi ini juga kerap digunakan untuk kegiatan tradisi bersih desa dan upacara adat.', '1751364613_WhatsApp Image 2025-07-01 at 16.01.50_f5da4c20.jpg,1751425052_0.jpg', '2025-06-03 04:03:32', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d753.4467704609389!2d110.51321510536395!3d-7.402928387062308!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a7b6365a098f9%3A0x4a914e6e638f8fe4!2sRTH%20Klero!5e1!3m2!1sen!2sid!4v1751427135058!5m2!1sen!2sid\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(2, 'RTH', 'RTH ialah singkatan dari Ruang Terbuka Hijau. Ini adalah area yang memanjang atau berkelompok yang bersifat terbuka, dan digunakan untuk menumbuhkan tanaman, baik yang tumbuh secara alami maupun yang sengaja ditanam. RTH memiliki fungsi penting dalam menjaga kualitas lingkungan, termasuk meningkatkan kualitas udara, mengatur suhu, dan menyediakan ruang rekreasi bagi masyarakat.  ', 'RTHjpg.jpg,1751424196_0.jpg,1751424196_1.jpg,1751424196_2.jpg', '2025-06-04 03:57:10', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d753.4467704609389!2d110.51321510536395!3d-7.402928387062308!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a7b6365a098f9%3A0x4a914e6e638f8fe4!2sRTH%20Klero!5e1!3m2!1sen!2sid!4v1751427135058!5m2!1sen!2sid\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>'),
(16, 'Candi Klero', 'Candi Klero adalah situs bersejarah peninggalan zaman Hindu yang terletak di Desa Klero, Kabupaten Semarang. Meskipun ukurannya tidak sebesar candi-candi terkenal lain di Jawa Tengah, Candi Klero menyimpan nilai arkeologis dan budaya yang tinggi. Candi ini terbuat dari batu andesit dan memiliki struktur yang sederhana, menunjukkan pengaruh arsitektur klasik masa lampau. Keberadaan candi ini menjadi bukti penting jejak peradaban Hindu di wilayah Semarang dan sering dikunjungi oleh pelajar, peneliti, maupun wisatawan yang tertarik dengan sejarah dan budaya lokal.', '1751424311_0_68649d37f2e1a.jpg,1751424947_0.jpg,1751424947_1.jpg', '2025-07-02 02:45:11', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1615.6843886251493!2d110.5198626390462!3d-7.412504536501904!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a7a789e9919ff%3A0x888bd50ae873fdbe!2sKlero%20Temple!5e1!3m2!1sen!2sid!4v1751426759161!5m2!1sen!2sid\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `umkm`
--
ALTER TABLE `umkm`
  ADD PRIMARY KEY (`id_umkm`);

--
-- Indexes for table `wisata`
--
ALTER TABLE `wisata`
  ADD PRIMARY KEY (`id_wisata`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `umkm`
--
ALTER TABLE `umkm`
  MODIFY `id_umkm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `wisata`
--
ALTER TABLE `wisata`
  MODIFY `id_wisata` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
