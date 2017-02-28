-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Czas generowania: 28 Lut 2017, 19:50
-- Wersja serwera: 10.1.16-MariaDB
-- Wersja PHP: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `mynote`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `note`
--

CREATE TABLE `note` (
  `note_id` int(11) NOT NULL,
  `note_section_id` int(11) NOT NULL,
  `note_author_id` int(11) NOT NULL,
  `note_author` varchar(127) COLLATE utf8_polish_ci NOT NULL DEFAULT 'Gość',
  `note_title` text COLLATE utf8_polish_ci NOT NULL,
  `note_content` text COLLATE utf8_polish_ci NOT NULL,
  `note_creation_datetime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `note_removed` tinyint(1) DEFAULT '0' COMMENT '0 - notatka istnieje, 1 - notatkę usunięto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci COMMENT='Tabela Notatki';

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `section`
--

CREATE TABLE `section` (
  `section_id` int(11) NOT NULL,
  `section_author_id` int(11) NOT NULL,
  `section_fullname` varchar(127) COLLATE utf8_polish_ci NOT NULL,
  `section_color` varchar(17) COLLATE utf8_polish_ci NOT NULL DEFAULT 'default',
  `section_visibility` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - prywatna, 1 - publiczna',
  `section_removed` tinyint(1) DEFAULT '0' COMMENT '0 - sekcja istnieje, 1 - sekcję usunięto',
  `section_order` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_username` varchar(50) COLLATE utf8_polish_ci NOT NULL,
  `user_password` varchar(32) COLLATE utf8_polish_ci DEFAULT NULL,
  `user_password_salt` varchar(23) COLLATE utf8_polish_ci NOT NULL COMMENT 'uniqid()',
  `user_register_datetime` datetime NOT NULL,
  `user_last_login_datetime` datetime DEFAULT NULL,
  `user_email` varchar(64) COLLATE utf8_polish_ci NOT NULL,
  `user_name` varchar(32) COLLATE utf8_polish_ci DEFAULT NULL,
  `user_last_name` varchar(64) COLLATE utf8_polish_ci DEFAULT NULL,
  `user_role` varchar(10) COLLATE utf8_polish_ci NOT NULL COMMENT 'rola użytkownika',
  `user_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - użytkownik aktywny, 1 - zablokowany'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indexes for table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `note_section_id` (`note_section_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`section_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_username` (`user_username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `note`
--
ALTER TABLE `note`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT dla tabeli `section`
--
ALTER TABLE `section`
  MODIFY `section_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
