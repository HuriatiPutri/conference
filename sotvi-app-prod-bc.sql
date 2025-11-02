-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 02, 2025 at 05:48 PM
-- Server version: 11.4.5-MariaDB-ubu2404
-- PHP Version: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sotvi-app`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `level` varchar(255) NOT NULL DEFAULT 'info',
  `message` varchar(255) NOT NULL,
  `context` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`context`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `level`, `message`, `context`, `created_at`, `updated_at`) VALUES
(3, 'SUCCESS', 'PayPal Order Completed', '{\"id\":\"5MK73061H89041910\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"disbursement_mode\":\"INSTANT\",\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"status\":\"COMPLETED\",\"supplementary_data\":{\"related_ids\":[]},\"payee\":{\"email_address\":\"aldealanda@gmail.com\",\"merchant_id\":\"LHBS7K526NC9Q\"},\"create_time\":\"2025-08-29T14:05:54Z\",\"update_time\":\"2025-08-29T14:05:54Z\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/5MK73061H89041910\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/5MK73061H89041910\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"}]}', '2025-08-30 15:35:14', '2025-08-30 15:35:14'),
(4, 'SUCCESS', 'PayPal Order Completed', '{\"id\":\"5MK73061H89041910\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"disbursement_mode\":\"INSTANT\",\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"status\":\"COMPLETED\",\"supplementary_data\":{\"related_ids\":[]},\"payee\":{\"email_address\":\"aldealanda@gmail.com\",\"merchant_id\":\"LHBS7K526NC9Q\"},\"create_time\":\"2025-08-29T14:05:54Z\",\"update_time\":\"2025-08-29T14:05:54Z\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/5MK73061H89041910\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/5MK73061H89041910\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"}]}', '2025-08-30 15:51:05', '2025-08-30 15:51:05'),
(5, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"7WU88110UW614000E\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7WU88110UW614000E\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=7WU88110UW614000E\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7WU88110UW614000E\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7WU88110UW614000E\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-08-31 08:23:26', '2025-08-31 08:23:26'),
(6, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"7WU88110UW614000E\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"azlin.ramli.study@gmail.com\",\"account_id\":\"Q57WCTDADLYSJ\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"AZLIN\",\"surname\":\"RAMLI\"},\"address\":{\"country_code\":\"MY\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-68b4067d9c78b\",\"shipping\":{\"name\":{\"full_name\":\"AZLIN RAMLI\"},\"address\":{\"address_line_1\":\"5 Jalan Kerongsang 11\\/6b\",\"address_line_2\":\"SEKSYEN 11\",\"admin_area_2\":\"Shah Alam\",\"admin_area_1\":\"SELANGOR\",\"postal_code\":\"40100\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"6TJ59759E8286304L\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/6TJ59759E8286304L\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/6TJ59759E8286304L\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7WU88110UW614000E\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-08-31T08:27:08Z\",\"update_time\":\"2025-08-31T08:27:08Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"AZLIN\",\"surname\":\"RAMLI\"},\"email_address\":\"azlin.ramli.study@gmail.com\",\"payer_id\":\"Q57WCTDADLYSJ\",\"address\":{\"country_code\":\"MY\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7WU88110UW614000E\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-08-31 08:27:08', '2025-08-31 08:27:08'),
(7, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"8X0949222F4858350\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8X0949222F4858350\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=8X0949222F4858350\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8X0949222F4858350\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8X0949222F4858350\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-08-31 15:10:31', '2025-08-31 15:10:31'),
(8, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"8X0949222F4858350\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"ashikinmrom@gmail.com\",\"account_id\":\"G5DL5G4D2TYA8\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"Noor Ashikin\",\"surname\":\"Mohd Rom\"},\"address\":{\"country_code\":\"MY\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-68b465e635dea\",\"shipping\":{\"name\":{\"full_name\":\"Noor Ashikin Mohd Rom\"},\"address\":{\"address_line_1\":\"Faculty of Management\",\"address_line_2\":\"Multimedia University\",\"admin_area_2\":\"Cyberjaya\",\"admin_area_1\":\"Selangor\",\"postal_code\":\"63100\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"1PR25931YE872252N\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/1PR25931YE872252N\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/1PR25931YE872252N\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8X0949222F4858350\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-08-31T15:17:55Z\",\"update_time\":\"2025-08-31T15:17:55Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Noor Ashikin\",\"surname\":\"Mohd Rom\"},\"email_address\":\"ashikinmrom@gmail.com\",\"payer_id\":\"G5DL5G4D2TYA8\",\"address\":{\"country_code\":\"MY\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8X0949222F4858350\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-08-31 15:17:56', '2025-08-31 15:17:56'),
(9, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"7KH966082S8542542\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7KH966082S8542542\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=7KH966082S8542542\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7KH966082S8542542\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7KH966082S8542542\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
(10, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"0RY026413W203143S\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/0RY026413W203143S\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=0RY026413W203143S\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/0RY026413W203143S\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/0RY026413W203143S\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
(11, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"01C71363WU0104312\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/01C71363WU0104312\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=01C71363WU0104312\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/01C71363WU0104312\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/01C71363WU0104312\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
(12, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"4P605616U56750214\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/4P605616U56750214\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=4P605616U56750214\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/4P605616U56750214\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/4P605616U56750214\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
(13, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"6NF89949KN3181351\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/6NF89949KN3181351\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=6NF89949KN3181351\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/6NF89949KN3181351\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/6NF89949KN3181351\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
(14, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"38393290CD694182N\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/38393290CD694182N\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=38393290CD694182N\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/38393290CD694182N\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/38393290CD694182N\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
(15, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"01C71363WU0104312\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"c.waishiang@gmail.com\",\"account_id\":\"VTXJGE6D9V7Y6\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"cheah\",\"surname\":\"waishiang\"},\"address\":{\"country_code\":\"MY\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-68b5427956f8a\",\"shipping\":{\"name\":{\"full_name\":\"cheah wai shiang\"},\"address\":{\"address_line_1\":\"Universiti Malaysia Sarawak\",\"address_line_2\":\"Faculty of Computer Science & IT\",\"admin_area_2\":\"kota samarahan\",\"admin_area_1\":\"Sarawak\",\"postal_code\":\"94300\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"94P86059M5329750N\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/94P86059M5329750N\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/94P86059M5329750N\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/01C71363WU0104312\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-09-01T06:53:12Z\",\"update_time\":\"2025-09-01T06:53:12Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"cheah\",\"surname\":\"waishiang\"},\"email_address\":\"c.waishiang@gmail.com\",\"payer_id\":\"VTXJGE6D9V7Y6\",\"address\":{\"country_code\":\"MY\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/01C71363WU0104312\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-09-01 06:53:13', '2025-09-01 06:53:13'),
(16, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"7VB005232N634515V\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7VB005232N634515V\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=7VB005232N634515V\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7VB005232N634515V\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/7VB005232N634515V\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-02 06:16:16', '2025-09-02 06:16:16'),
(17, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"5LW98467292906102\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/5LW98467292906102\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=5LW98467292906102\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/5LW98467292906102\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/5LW98467292906102\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-02 06:53:07', '2025-09-02 06:53:07'),
(18, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"5LW98467292906102\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"suchenghaw76@gmail.com\",\"account_id\":\"R7TY9AY9V7CEY\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"Su Cheng\",\"surname\":\"Haw\"},\"address\":{\"country_code\":\"MY\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-68b69452dddcb\",\"shipping\":{\"name\":{\"full_name\":\"Haw Su Cheng\"},\"address\":{\"address_line_1\":\"Faculty of Computing and Informatics\",\"address_line_2\":\"Multimedia University\",\"admin_area_2\":\"Cyberjaya\",\"admin_area_1\":\"SELANGOR\",\"postal_code\":\"63100\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"73E61013DY4399917\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/73E61013DY4399917\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/73E61013DY4399917\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/5LW98467292906102\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-09-02T07:01:28Z\",\"update_time\":\"2025-09-02T07:01:28Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Su Cheng\",\"surname\":\"Haw\"},\"email_address\":\"suchenghaw76@gmail.com\",\"payer_id\":\"R7TY9AY9V7CEY\",\"address\":{\"country_code\":\"MY\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/5LW98467292906102\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-09-02 07:01:29', '2025-09-02 07:01:29'),
(19, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"49K19726VP7312548\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/49K19726VP7312548\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=49K19726VP7312548\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/49K19726VP7312548\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/49K19726VP7312548\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-09-02 13:37:40', '2025-09-02 13:37:40'),
(20, 'SUCCESS', 'PayPal Order Completed', '{\"id\":\"71B82969J7007983R\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"invoice_id\":\"INV2-VV3J-6DGR-SR2Y-6CMA\",\"status\":\"COMPLETED\",\"supplementary_data\":{\"related_ids\":{\"order_id\":\"63H99550UX5811340\"}},\"payee\":{\"email_address\":\"aldealanda@gmail.com\",\"merchant_id\":\"LHBS7K526NC9Q\"},\"create_time\":\"2025-09-03T05:27:36Z\",\"update_time\":\"2025-09-03T05:27:36Z\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/71B82969J7007983R\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/71B82969J7007983R\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/63H99550UX5811340\",\"rel\":\"up\",\"method\":\"GET\"}]}', '2025-09-03 11:22:31', '2025-09-03 11:22:31'),
(21, 'SUCCESS', 'PayPal Order Completed', '{\"id\":\"71B82969J7007983R\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"invoice_id\":\"INV2-VV3J-6DGR-SR2Y-6CMA\",\"status\":\"COMPLETED\",\"supplementary_data\":{\"related_ids\":{\"order_id\":\"63H99550UX5811340\"}},\"payee\":{\"email_address\":\"aldealanda@gmail.com\",\"merchant_id\":\"LHBS7K526NC9Q\"},\"create_time\":\"2025-09-03T05:27:36Z\",\"update_time\":\"2025-09-03T05:27:36Z\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/71B82969J7007983R\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/71B82969J7007983R\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/63H99550UX5811340\",\"rel\":\"up\",\"method\":\"GET\"}]}', '2025-09-03 11:30:32', '2025-09-03 11:30:32'),
(22, 'SUCCESS', 'PayPal Order Completed', '{\"id\":\"8VX25247JR942102K\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"invoice_id\":\"INV2-YMTR-YUEZ-FM9Y-XAGD\",\"status\":\"COMPLETED\",\"supplementary_data\":{\"related_ids\":{\"order_id\":\"12C777677U279361A\"}},\"payee\":{\"email_address\":\"aldealanda@gmail.com\",\"merchant_id\":\"LHBS7K526NC9Q\"},\"create_time\":\"2025-09-03T08:16:51Z\",\"update_time\":\"2025-09-03T08:16:51Z\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/8VX25247JR942102K\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/8VX25247JR942102K\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/12C777677U279361A\",\"rel\":\"up\",\"method\":\"GET\"}]}', '2025-09-03 11:34:25', '2025-09-03 11:34:25'),
(23, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"1XK163913K847391H\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/1XK163913K847391H\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=1XK163913K847391H\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/1XK163913K847391H\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/1XK163913K847391H\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-10-26 21:22:42', '2025-10-26 21:22:42'),
(24, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"1XK163913K847391H\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"ashikinmrom@gmail.com\",\"account_id\":\"G5DL5G4D2TYA8\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"Noor Ashikin\",\"surname\":\"Mohd Rom\"},\"address\":{\"country_code\":\"MY\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-68fef38f813e9\",\"shipping\":{\"name\":{\"full_name\":\"Noor Ashikin Mohd Rom\"},\"address\":{\"address_line_1\":\"Faculty of Management\",\"address_line_2\":\"Multimedia University\",\"admin_area_2\":\"Cyberjaya\",\"admin_area_1\":\"Selangor\",\"postal_code\":\"63100\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"6HA96661B7933582X\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/6HA96661B7933582X\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/6HA96661B7933582X\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/1XK163913K847391H\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-10-27T04:32:17Z\",\"update_time\":\"2025-10-27T04:32:17Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Noor Ashikin\",\"surname\":\"Mohd Rom\"},\"email_address\":\"ashikinmrom@gmail.com\",\"payer_id\":\"G5DL5G4D2TYA8\",\"address\":{\"country_code\":\"MY\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/1XK163913K847391H\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-10-26 21:32:19', '2025-10-26 21:32:19'),
(25, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"2FX61831VA233803X\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/2FX61831VA233803X\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=2FX61831VA233803X\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/2FX61831VA233803X\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/2FX61831VA233803X\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-10-26 21:45:18', '2025-10-26 21:45:18'),
(26, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"2FX61831VA233803X\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"mosen_cray04@yahoo.com\",\"account_id\":\"6GARJMNVVLZMA\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"Mohsen\",\"surname\":\"Mohamad Hata\"},\"address\":{\"country_code\":\"MY\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-68fef8ddadcb6\",\"shipping\":{\"name\":{\"full_name\":\"Mohsen Mohamad Hata\"},\"address\":{\"address_line_1\":\"B-13-10, The Era Residence\",\"address_line_2\":\"Jalan Segambut\",\"admin_area_2\":\"Kuala Lumpur\",\"admin_area_1\":\"Kuala Lumpur\",\"postal_code\":\"51200\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"38P24262MA418542G\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/38P24262MA418542G\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/38P24262MA418542G\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/2FX61831VA233803X\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-10-27T05:22:11Z\",\"update_time\":\"2025-10-27T05:22:11Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Mohsen\",\"surname\":\"Mohamad Hata\"},\"email_address\":\"mosen_cray04@yahoo.com\",\"payer_id\":\"6GARJMNVVLZMA\",\"address\":{\"country_code\":\"MY\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/2FX61831VA233803X\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-10-26 22:22:12', '2025-10-26 22:22:12'),
(27, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"8M3424740X671154P\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8M3424740X671154P\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=8M3424740X671154P\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8M3424740X671154P\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8M3424740X671154P\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-10-27 07:19:05', '2025-10-27 07:19:05'),
(28, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"8M3424740X671154P\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"sales@ebusinessoft.com\",\"account_id\":\"V5D5XSVGTTFMN\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"Hoo Thye\",\"surname\":\"Neoh\"},\"business_name\":\"Businessoft Pte Ltd\",\"address\":{\"country_code\":\"SG\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-68ff7f58466c5\",\"shipping\":{\"name\":{\"full_name\":\"Khye Neoh\"},\"address\":{\"address_line_1\":\"11-5-A, T-Parkland,\",\"address_line_2\":\"Persiaran Bukit Takun 2,\",\"admin_area_2\":\"Templer Park, Rawang\",\"admin_area_1\":\"SELANGOR\",\"postal_code\":\"48000\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"3RW12188TT619901D\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/3RW12188TT619901D\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/3RW12188TT619901D\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8M3424740X671154P\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-10-27T14:19:20Z\",\"update_time\":\"2025-10-27T14:19:20Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Hoo Thye\",\"surname\":\"Neoh\"},\"email_address\":\"sales@ebusinessoft.com\",\"payer_id\":\"V5D5XSVGTTFMN\",\"address\":{\"country_code\":\"SG\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/8M3424740X671154P\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-10-27 07:19:21', '2025-10-27 07:19:21'),
(29, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"9LA179889C4652548\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/9LA179889C4652548\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=9LA179889C4652548\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/9LA179889C4652548\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/9LA179889C4652548\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-10-27 07:27:37', '2025-10-27 07:27:37'),
(30, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"25E51524MN581623N\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/25E51524MN581623N\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=25E51524MN581623N\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/25E51524MN581623N\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/25E51524MN581623N\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-10-27 22:49:21', '2025-10-27 22:49:21'),
(31, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"25E51524MN581623N\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shuzlina@fskm.uitm.edu.my\",\"account_id\":\"VZEC4CD4FNMES\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"SHUZLINA\",\"surname\":\"ABDUL RAHMAN\"},\"address\":{\"country_code\":\"MY\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-6900596018409\",\"shipping\":{\"name\":{\"full_name\":\"SHUZLINA ABDUL RAHMAN\"},\"address\":{\"address_line_1\":\"No. 24, Jalan Tun Teja 35\\/2A\",\"address_line_2\":\"Alam Impian\",\"admin_area_2\":\"Shah Alam\",\"admin_area_1\":\"SELANGOR\",\"postal_code\":\"40470\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"79B9646967354072S\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/79B9646967354072S\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/79B9646967354072S\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/25E51524MN581623N\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-10-28T05:56:32Z\",\"update_time\":\"2025-10-28T05:56:32Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"SHUZLINA\",\"surname\":\"ABDUL RAHMAN\"},\"email_address\":\"shuzlina@fskm.uitm.edu.my\",\"payer_id\":\"VZEC4CD4FNMES\",\"address\":{\"country_code\":\"MY\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/25E51524MN581623N\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-10-27 22:56:33', '2025-10-27 22:56:33'),
(32, 'CREATE-ORDER', 'PayPal Create Order Response', '{\"id\":\"717620086R714350L\",\"status\":\"CREATED\",\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/717620086R714350L\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/www.paypal.com\\/checkoutnow?token=717620086R714350L\",\"rel\":\"approve\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/717620086R714350L\",\"rel\":\"update\",\"method\":\"PATCH\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/717620086R714350L\\/capture\",\"rel\":\"capture\",\"method\":\"POST\"}]}', '2025-10-28 07:00:34', '2025-10-28 07:00:34'),
(33, 'CAPTURE', 'PayPal Capture Order Response', '{\"id\":\"717620086R714350L\",\"status\":\"COMPLETED\",\"payment_source\":{\"paypal\":{\"email_address\":\"shheng@mmu.edu.my\",\"account_id\":\"H4S626ZAUU2MY\",\"account_status\":\"VERIFIED\",\"name\":{\"given_name\":\"Swee Huay\",\"surname\":\"Heng\"},\"address\":{\"country_code\":\"MY\"}}},\"purchase_units\":[{\"reference_id\":\"ORDER-6900cc80bcf43\",\"shipping\":{\"name\":{\"full_name\":\"Swee Huay Heng\"},\"address\":{\"address_line_1\":\"No.32 Jalan Seri Mangga 1\\/1\",\"address_line_2\":\"Taman Seri Mangga Seksyen 1\",\"admin_area_2\":\"Melaka\",\"admin_area_1\":\"MELAKA\",\"postal_code\":\"75250\",\"country_code\":\"MY\"}},\"payments\":{\"captures\":[{\"id\":\"9DD81024AB911002K\",\"status\":\"COMPLETED\",\"amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"final_capture\":true,\"seller_protection\":{\"status\":\"ELIGIBLE\",\"dispute_categories\":[\"ITEM_NOT_RECEIVED\",\"UNAUTHORIZED_TRANSACTION\"]},\"seller_receivable_breakdown\":{\"gross_amount\":{\"currency_code\":\"USD\",\"value\":\"550.00\"},\"paypal_fee\":{\"currency_code\":\"USD\",\"value\":\"24.50\"},\"net_amount\":{\"currency_code\":\"USD\",\"value\":\"525.50\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/9DD81024AB911002K\",\"rel\":\"self\",\"method\":\"GET\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/payments\\/captures\\/9DD81024AB911002K\\/refund\",\"rel\":\"refund\",\"method\":\"POST\"},{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/717620086R714350L\",\"rel\":\"up\",\"method\":\"GET\"}],\"create_time\":\"2025-10-28T14:09:35Z\",\"update_time\":\"2025-10-28T14:09:35Z\"}]}}],\"payer\":{\"name\":{\"given_name\":\"Swee Huay\",\"surname\":\"Heng\"},\"email_address\":\"shheng@mmu.edu.my\",\"payer_id\":\"H4S626ZAUU2MY\",\"address\":{\"country_code\":\"MY\"}},\"links\":[{\"href\":\"https:\\/\\/api.paypal.com\\/v2\\/checkout\\/orders\\/717620086R714350L\",\"rel\":\"self\",\"method\":\"GET\"}]}', '2025-10-28 07:09:36', '2025-10-28 07:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `audiences`
--

CREATE TABLE `audiences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(100) NOT NULL,
  `conference_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `paper_title` varchar(255) DEFAULT NULL,
  `institution` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `country` varchar(255) NOT NULL,
  `presentation_type` varchar(255) NOT NULL,
  `paid_fee` decimal(10,2) DEFAULT NULL,
  `payment_status` enum('pending_payment','paid','cancelled','refunded') NOT NULL DEFAULT 'pending_payment',
  `payment_method` enum('transfer_bank','payment_gateway') DEFAULT NULL,
  `payment_proof_path` varchar(255) DEFAULT NULL,
  `full_paper_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audiences`
--

INSERT INTO `audiences` (`id`, `public_id`, `conference_id`, `first_name`, `last_name`, `paper_title`, `institution`, `email`, `phone_number`, `country`, `presentation_type`, `paid_fee`, `payment_status`, `payment_method`, `payment_proof_path`, `full_paper_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(86, '6896a41721a4e', 5, 'Aldo', 'Erianda', 'SLR Mantap', 'Politeknik Negeri Padang', 'aldo@pnp.ac.id', '085263791200', 'ID', 'online_author', 555000.00, 'pending_payment', 'payment_gateway', NULL, NULL, '2025-08-09 01:27:51', '2025-10-24 05:09:35', '2025-10-24 05:09:35'),
(88, '68a071604a77c', 5, 'Rahmat', 'Hidayat', 'Test', 'PNP', 'rahmat@pnp.ac.id', '82171822448', 'ID', 'participant_only', 51000.00, 'pending_payment', 'transfer_bank', NULL, NULL, '2025-08-16 11:54:08', '2025-10-24 05:09:52', '2025-10-24 05:09:52'),
(89, '68a072b9e8a51', 5, 'Rahmat', 'Hidayat', 'Test Paper', 'PNP', 'rahmat@sotvi.org', '82171822448', 'ID', 'online_author', 555000.00, 'paid', 'payment_gateway', NULL, NULL, '2025-08-16 11:59:53', '2025-10-24 05:10:01', '2025-10-24 05:10:01'),
(90, '68a1cc54b9be7', 5, 'aasdasd', 'asdasd', 'asdasdasdasdas', 'asdasdasda', 'asdasd@gmail.com', NULL, 'AI', 'onsite', 555000.00, 'pending_payment', 'payment_gateway', NULL, NULL, '2025-08-17 12:34:28', '2025-10-24 05:12:33', '2025-10-24 05:12:33'),
(91, '68a1cd2622c11', 5, 'Alde', 'Alanda', 'SCOTZ', 'SOTVI', 'alde@pnp.ac.id', NULL, 'AG', 'participant_only', 51000.00, 'paid', 'payment_gateway', NULL, NULL, '2025-08-17 12:37:58', '2025-10-24 05:12:40', '2025-10-24 05:12:40'),
(96, '68aae02a063a1', 10, 'Aldo', 'Erianda', 'SLR Tak Sudah-sudah', 'Politeknik Negeri Padang', 'erianda89@gmail.com', '085263791200', 'ID', 'online_author', 8500000.00, 'pending_payment', 'payment_gateway', NULL, NULL, '2025-08-24 09:49:30', '2025-09-03 00:17:37', '2025-09-03 00:17:37'),
(101, '68ada01bbc73c', 10, 'Aldo', 'Erianda', 'Bla bla', 'PNP', 'aldo@pnp.ac.id', '085263791200', 'MY', 'onsite', 550.00, 'pending_payment', 'payment_gateway', NULL, NULL, '2025-08-26 11:52:59', '2025-09-03 00:18:04', '2025-09-03 00:18:04'),
(103, '68adae75859f3', 10, 'M. Khairul', 'Anam', 'Hybrid CNN-BiLSTM-Machine Learning Architecture (HACBML) for Enhancing Anomaly Detection in Server Security', 'Universitas Samudra', 'khairulanam@unsam.ac.id', '081363769937', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/X8lIQq0Kbjh2Rpbpunk0VBfPWV7PosGZAA2AJxWm.jpg', 'audience_full_papers/jlgKur6nsXGKCYphJ9rnfj7mJoKHyd8VZquZGtbW.docx', '2025-08-26 12:54:13', '2025-08-29 05:10:06', NULL),
(104, '68ae36f1ebbe3', 10, 'Yufis', 'Azhar', 'Batik Motif Classification using Textual Inversion in Low-Data Settings', 'Universitas Muhammadiyah Malang', 'yufis@umm.ac.id', '+6285790809961', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/1FsfSZSdgoNoEmIlC9NnIwmGuIqZdlB2SZbc8BeS.jpg', 'audience_full_papers/s5M2CGrfVTCS2h1KM6lINpid6BgyEdFfAIEpZ7tH.docx', '2025-08-26 22:36:33', '2025-08-29 05:09:13', NULL),
(106, '68afa34c4a1b7', 10, 'Ninuk', 'Wiliani', 'Optimizing Convolutional Neural Network for Surface Defect Detection on Solar Panels in Smart Farming Applications', 'University of Pancasila', 'ninuk.wiliani@univpancasila.ac.id', '+6285218111574', 'ID', 'online_author', 8500000.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/iIT8UOFbFyOb01abRgazW8cHmpigebVBqIqqX1x5.docx', '2025-08-28 00:31:09', '2025-08-28 00:50:46', NULL),
(107, '68afd2ae9259d', 10, 'Risqy Siwi', 'Pradini', 'Optimizing the Performance of K-Nearest Neighbors for Diabetes Classification in Women Through GridSearchCV-Based Hyperparameter Tuning', 'Institut Teknologi, Sains, dan Kesehatan RS.DR. Soepraoen Kesdam V/BRW', 'risqypradini@itsk-soepraoen.ac.id', '085733333284', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/8eSoM5EPV2U6bs2jTV0OCCVCLh9JIjWlE1s8K4rt.jpg', 'audience_full_papers/Z5GWfasq8cotvSPHuWmeNIXQ4InECVVk4XQzV6fj.docx', '2025-08-28 03:53:18', '2025-08-29 05:07:22', NULL),
(108, '68afe93a423f4', 10, 'I Wayan', 'Santiyasa', 'A Data-Driven Multi-Sensor Detection for Machine Learning Recommendation: Case Study of Controlled Agriculture on Volcanic Land in Indonesia', 'Udayana University', 'santiyasa@unud.ac.id', '+6282266348281', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/m15iz25hOUFcwLyhiSZw8nqlnvMztmeYc9jvhtFT.jpg', 'audience_full_papers/2hbz1lmDIM2rLoI53RyRepP7zMGDFszQAuevCHUI.docx', '2025-08-28 05:29:30', '2025-08-29 05:07:01', NULL),
(109, '68aff5d5969d2', 10, 'Dr.Ir.Putri', 'Prasetyaningrum,S.T.,M.T,,MCE,MCF', 'Adaptive Gamified Virtual Reality Framework to Enhance Student Engagement and Accessibility in Educational Counseling', 'Universitas Mercu Buana Yogyakarta', 'putri@mercubuana-yogya.ac.id', '081329345556', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/SsICSmDhjcMQQrh8bEu3MeTSPqp7VnmMwQNFuOGj.jpg', 'audience_full_papers/xU7GHNtx4EuNUgZoRz1rrXH3xx2bq4E3jAATjYvy.doc', '2025-08-28 06:23:17', '2025-08-29 03:54:19', NULL),
(110, '68b04ba4801e9', 10, 'Duman Care', 'Khrisne', 'Traffic Signal Control via Proximal Policy Optimization with Reward Shaping to Minimize Waiting Time and Violations', 'Udayana University', 'duman@unud.ac.id', '085739269030', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/JG81Y8NGh9Aac6Hr3ZndB8vhADF2nM6lTvRidPMa.jpg', 'audience_full_papers/y5wG9bacxVD6sV5aX2hIpQpysxsaESygqPjN7P2l.docx', '2025-08-28 12:29:24', '2025-08-29 05:05:18', NULL),
(114, '68b12c4eb2c76', 10, 'Widi', 'Sarinastiti', 'Analyzing the Effect of Age and Experience on Usability Performance', 'Politeknik Elektronika Negeri Surabaya', 'widisarinastiti@pens.ac.id', '08113141444', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/diX6SRO7sdA0sGEM0jINHY08V2MpafgkXnHIwEbQ.jpg', 'audience_full_papers/gbLCCdPjniVYlliJxRNwA0LbkicfXetX9ByFfjhF.doc', '2025-08-29 04:27:58', '2025-08-29 05:04:50', NULL),
(116, '68b13351b7668', 10, 'Dewa Made', 'Wiharta', 'RANSAC as a Robust Filter for Clock Skew Estimation', 'Udayana University', 'wiharta@unud.ac.id', '+62 817-0344-0558', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/cAeIbRpKMN7IdmYXp0jaDCUZZeH626nOEiVvYX0E.jpg', 'audience_full_papers/pFqWGJi8RpAHkOvzzKddQ1rnqnla0DfesMXn495o.docx', '2025-08-29 04:57:53', '2025-08-29 05:04:31', NULL),
(117, '68b1360e10a00', 10, 'Syarifah Bahiyah', 'Rahayu', 'DeepNest-SCAN: An Unsupervised Surface Crack Anomaly Detection using Nested Autoencoders', 'UPNM', 'syarifahbahiyah@upnm.edu.my', '0103535053', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, NULL, '2025-08-29 05:09:34', '2025-08-29 05:22:17', NULL),
(118, '68b14f3d91bb0', 10, 'Nor Fyadzillah', 'Mohd Taha', 'A Systematic Review of Adaptive ABC Inventory Models in Food Manufacturing: Integrating MCDM, IoT, and Machine Learning', 'Universiti Pertahanan Nasional Malaysia', 'norfyadzillah@upnm.edu.my', '0197506567', 'MY', 'onsite', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/i85friyXwJgdzkWLE89BUXzebJL85ifK214jVdPg.png', 'audience_full_papers/YMptSw26qK3kZBQbPZlEdEsv9TwHOUHw0AXnVy44.doc', '2025-08-29 06:57:01', '2025-08-29 06:57:01', NULL),
(119, '68b153f0f2333', 10, 'Widyadi', 'Setiawan', 'Accelerated Detection of Vessel Loitering Behavior Using GPU-Based Processing of Automatic Identification System Data', 'Universitas Udayana', 'widyadi@unud.ac.id', '08123636625', 'ID', 'online_author', 8500000.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/unzGuYa6uhdlKMgmbSkHNFHjVG2rUgv12r7a7XHZ.doc', '2025-08-29 07:17:04', '2025-08-29 07:23:09', NULL),
(120, '68b165f7276bf', 10, 'RUZANNA', 'MAT JUSOH', '(1571183604) Inappropriate Prescribing in Older Diabetic Patients: A Decision Analysis Approach)', 'UNIVERSITI PERTAHANAN NASIONAL MALAYSIA', 'ruzanna@upnm.edu.my', '0179896200', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/KMeKZeQTx40R9y5YEyolUtqzEKAHMNPABGjqSIr3.jpg', 'audience_full_papers/70gcx4poVfJamJGye09lzJmavi3F8ZouPXEmqXOK.docx', '2025-08-29 08:33:59', '2025-08-29 08:33:59', NULL),
(121, '68b1ae4670840', 10, 'Muhamad Lazim', 'bin Talib', 'Digital Fingerprints: Tracing Image Provenance from Sensor Pattern Noise to Generative Model Artefacts', 'NATIONAL DEFENCE UNIVERSITY OF MALAYSIA', 'lazim@upnm.edu.my', '0123730251', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/VKDo0PMvVNYXup5wwjipz93YFWpVICIVOJaa1wYP.pdf', NULL, '2025-08-29 13:42:30', '2025-08-29 13:42:30', NULL),
(122, '68b1b3906dd4a', 10, 'CHUN YANG', 'LAW', 'The Analysis of Succession Planning From a Talent Management Perspectives Using PLS-SEM: Its Influence on Employee Performance with Mediating Role of Organizational Culture in Education', 'National Defence University of Malaysia', 'lawchunyang@gmail.com', '0182883087', 'MY', 'onsite', 550.00, 'pending_payment', 'payment_gateway', NULL, 'audience_full_papers/NxrJnPs1XkSCMJUVB9CfLSNziRh3v1pmrU4kUvvU.docx', '2025-08-29 14:05:04', '2025-08-29 14:05:04', NULL),
(123, '68b1b522b8c30', 10, 'CHEN CHUEN', 'LEE', 'Exploring Gaps in Cyber Resilience: A Systematic Literature Review of Human Factors and AI', 'Universiti Pertahanan National Malaysia', 'chenchuenlee3@gmail.com', '+6012 6096 121', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/jzECZlqbEIF1JelspkThHkpONefmoP1fFGa29etK.docx', '2025-08-29 14:11:46', '2025-08-30 15:51:00', NULL),
(124, '68b1b640c0fce', 10, 'Suzaimah', 'Ramli', 'Classification of Surface Defects in Solar Panels Using GLCM Texture Features with KNN and Naive Bayes Classifiers', 'Universiti Pertahanan Nasional Malaysia', 'suzaimah@upnm.edu.my', '0193569265', 'MY', 'online_author', 550.00, 'paid', 'transfer_bank', 'payment_proofs/zluuKjkxJhEzMttWPe4eM9u4o2KEC3Xj72G1tLkg.jpg', 'audience_full_papers/qlG0osC02ZLKPtpj3270wduC8F2SydLpfWoSj0q6.docx', '2025-08-29 14:16:32', '2025-09-11 05:23:29', NULL),
(125, '68b1b7b89eab9', 10, 'CHUN YANG', 'LAW', 'The Analysis of Succession Planning From a Talent Management Perspectives Using PLS-SEM: Its Influence on Employee Performance with Mediating Role of Organizational Culture in Education', 'National Defence University of Malaysia', '3241902@alfateh.upnm.edu.my', '0182883087', 'MY', 'onsite', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/PtjTFqwSFmk3EPlzuQ96j63UB03K00Yacowkgr7C.jpg', 'audience_full_papers/ORkyS9riHDWEZXiw85xcz64sYsa5wBQKfAkIShg8.docx', '2025-08-29 14:22:48', '2025-09-03 12:30:55', '2025-09-03 12:30:55'),
(126, '68b1c67e6ba2e', 10, 'MOHD FAIZAL', 'MUSTAFA', 'Enhancing Cybersecurity in Campus Networks: Integration of AI in Log Consolidation Processing (LCP) Framework for Network Threat Detection and Mitigation', 'National Defence University of Malaysia', 'mohdfaizal@upnm.edu.my', '60133564996', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/eNzsMj0K5BLI5ZrTK4T53x60TKhBGBa3GClRtZiX.pdf', 'audience_full_papers/cXQiHKQyVaYETn30teqBwCcWd50RzPAqCSibhRbk.docx', '2025-08-29 15:25:50', '2025-08-29 15:25:50', NULL),
(127, '68b1d7d3d74b3', 10, 'A\'in Hazwani', 'Ahmad Rizal', NULL, 'National Defense University of Malaysia', 'ainrizal98@gmail.com', '60125672415', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/W0SXYLqacAu7K5nbhIYGGwlhsAQitXNWYBhd0ZsX.jpg', NULL, '2025-08-29 16:39:47', '2025-08-29 16:39:47', NULL),
(128, '68b2255288aa5', 10, 'Mukhtar', 'Nazifi Imam', 'Advancing Military Intelligence Operations: Cyber Threat Detection and Response Strategies', 'Universiti Pertahanan Nasional Malaysia', 'khairynazif@gmail.com', '+601111405860', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/XG31e6mP7kBRzIqpkgIhh701LlmG6HiEzKIvRwC2.jpg', 'audience_full_papers/8pGzAS1T9qebp75ku7fptkAYHCp9qxuoKWXLiM5b.docx', '2025-08-29 22:10:26', '2025-08-29 22:10:26', NULL),
(129, '68b228d6671d4', 10, 'Mukhtar', 'Nazifi Imam', 'Automatic Criminal Threat Detection Via Body-worn Cameras Using Deep Learning Technique', 'Universiti Pertahanan Nasional Malaysia', 'mnazifi@rocketmail.com', '+601111405860', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/xHvHOizx0D9Ctfttb2ztUGABTHXGDCKGCANzlCoF.jpg', 'audience_full_papers/qo8WxgOSWg5DDdX4FX6S1QHZPzKUESinkgxF0Ilc.docx', '2025-08-29 22:25:26', '2025-08-29 22:25:26', NULL),
(130, '68b23b4b72612', 10, 'Agus Eko', 'Minarno', 'Enhancing BatikGAN through ESRGAN-Based Discriminator for High-Resolution Batik Pattern Synthesis', 'Universitas Muhammadiyah Malang', 'agoes.minarno@gmail.com', '081233084984', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/M13zX8trIIHKud5ApNPoxjOl7GyAuxrjt2vV2Dkt.jpg', 'audience_full_papers/LwhuFmeSIgr05LWZqgvGXGvOWEgPR5HePsUQlF4b.docx', '2025-08-29 23:44:11', '2025-08-30 09:34:15', NULL),
(131, '68b2666823cb0', 10, 'ANUSUYAH', 'SUBBARAO', 'Gen Z and Digital Learning: A Thematic Analysis of User Experience, Design Preferences, and Educational Impact', 'Multimedia University', 'anusuyah.subbarao@mmu.edu.my', '0163365934', 'MY', 'onsite', 550.00, 'paid', 'transfer_bank', 'payment_proofs/VYJEoPxNPaMtLYlsFeM4iVtEC6EmnzktAbhy3PNi.png', 'audience_full_papers/yxTqnGe0mDZwCUPQU88B754zPPxBoGCADFRgGHKJ.docx', '2025-08-30 02:48:08', '2025-09-03 00:11:44', NULL),
(132, '68b26aee3135b', 10, 'Nuzul', 'Hidayat', 'Arduino-Based Intelligent Control of Condensate Spraying on Condenser to Enhance COP in Automotive Air Conditioning System', 'Universitas Negeri Padang', 'nuzulhidayat@ft.unp.ac.id', '085278715287', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/2umQBohUXCpsHd79zEley3cQGuaaNfsmDZ3X6pqt.jpg', 'audience_full_papers/4Q15GGNIOxi7zipPIHrkJ4zvZ61Bl83Yy5BFqZ7G.docx', '2025-08-30 03:07:26', '2025-08-30 09:35:41', NULL),
(133, '68b2865c46697', 10, 'Awang Hendrianto', 'Pratomo', 'Application of EfficientNet Architecture for Geophysical Event Classification Using Transfer Learning Convolutional Neural Network Method', 'UPN \"Veteran\" Yogyakarta', 'awang@upnyk.ac.id', '081931767489', 'ID', 'onsite', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/m4rg4tj4yjHBc7kN4ol05Jz2m2Wh6b0c2tL5iHAM.jpg', 'audience_full_papers/GHTKKyihooPjZWeHjPuGyZ9vvHerHtF4lNG1lExr.docx', '2025-08-30 05:04:28', '2025-08-30 09:36:42', NULL),
(134, '68b28915ea75c', 10, 'Ari', 'Muzakir', 'Improved Performance of Cyberbullying Detection in Indonesian Using Sentence-Level Semantic Expansion', 'Universitas Bina Darma', 'arimuzakir@binadarma.ac.id', '6285273221414', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/JmnWmnruRsmkgohfk4Dc5d6fAgLIxNXmOvRDyhtE.jpg', 'audience_full_papers/vX685vy71ZOOsazMsBVc8yP9yNCJuGRlEfmhe0dC.docx', '2025-08-30 05:16:05', '2025-08-30 09:39:29', NULL),
(135, '68b2c2a55d7fc', 10, 'Hendri', 'Darmawan', 'Tes', 'Tes', 'hendridarmawan512@gmail.com', '089677030198', 'ID', 'online_author', 8500000.00, 'pending_payment', 'payment_gateway', NULL, NULL, '2025-08-30 09:21:41', '2025-09-03 12:16:35', '2025-09-03 12:16:35'),
(136, '68b2e3e02c0b1', 10, 'Dhio', 'Saputra', 'Real-Time Shuttlecock Detection and Scoring System in Badminton Using the Morphological Region of Development (MrD) Algorithm', 'University of Putra Indonesia YPTK', 'dhiosaputra@upiyptk.ac.id', '085271811005', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/OCPx5v9ejOYB7MmTDm8RUoiJ9VfHqATAkGltzJVs.jpg', 'audience_full_papers/aWYVxF6FDgd3EF2GwIsxlga4bMGc6MCWXQXAnyTi.docx', '2025-08-30 11:43:28', '2025-08-30 11:44:56', NULL),
(137, '68b2fdd9d1611', 10, 'RUHAIDAH', 'ABU NOR', 'Enhancing the Programming Syntax Connection Between Programming Language and Database System (PHP and Sybase)', 'National Defence University Malaysia UPNM', 'ruhaidah@upnm.edu.my', '0192201886', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/EC362PJawBwHnyMTsiUqi9L7a9R48taY1u1g7Heq.jpg', 'audience_full_papers/WzNBFKzSMLE2MrY02PYcTHS7zC0D8GcTXZtDne4z.docx', '2025-08-30 13:34:17', '2025-08-30 13:34:17', NULL),
(138, '68b303252313c', 10, 'Mohd Fahmi', 'Mohamad Amran', 'Emotion Classification in Special Needs Students Using EEG-Based Machine Learning Models', 'National Defence University of Malaysia', 'fahmiamran@upnm.edu.my', '123629449', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/cTGScTzz8CPQiJvIAQn0GM1nhdxwOmRXLAjfCgVx.jpg', 'audience_full_papers/IAPJZemecG22NuOwaomLEZkxFUySVntmPlRsMuFe.docx', '2025-08-30 13:56:53', '2025-08-30 13:56:53', NULL),
(139, '68b30ab3eb565', 10, 'Roziyani', 'Rawi', 'Performance Evaluation of Best Effort QoS in Multi-User Wireless Networks Using NetSim', 'Universiti Kuala Lumpur, National Defence University of Malaysia', 'roziyani@unikl.edu.my', '0135303009', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/Y6rZMDv6w3E89Veuu9ARzb8UrSSuYZssKArqOJLF.pdf', 'audience_full_papers/S1Ni1AlA46KQ882RMHHRWk6sPrrlYI5fhIb10vLi.docx', '2025-08-30 14:29:07', '2025-08-30 14:29:07', NULL),
(140, '68b31cd281653', 10, 'Arif', 'Basofi', 'A Mobile-Based Approach to Library Book Cataloguing: Leveraging OCR, AI, and Integrated RPA', 'Politeknik Elektronika Negeri Surabaya', 'ariv@pensc.ac.id', '081330485860', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/bw4ydgQgrqIGmve1CHZcjjCJXH1VZc3pGFfpnkNf.jpg', 'audience_full_papers/kDYihJscdAh74PCCIchpAtWk8670p0cQufzo12AU.docx', '2025-08-30 15:46:26', '2025-08-31 15:37:31', NULL),
(141, '68b3bad7d611c', 10, 'Mohd Sidek Fadhil', 'Mohd Yunus', 'DevSecOps in Practicem: Where the Framework Falls Short?', 'National Defence University of Malaysia (UPNM)', 'sidek@upnm.edu.my', '1161802941', 'MY', 'onsite', 550.00, 'paid', 'transfer_bank', 'payment_proofs/Qe4yIkxCBfddeI5P8CdPeGHnSDCJjDtjhlh9xOoj.png', 'audience_full_papers/tC5x3YItA1nJPrCyaQC43kgAVH0gzbTqNdzFwXGi.docx', '2025-08-31 03:00:39', '2025-09-09 05:19:44', NULL),
(142, '68b4067429df9', 10, 'AZLIN', 'RAMLI', 'Developing a Holistic ICT Security Framework for Enhancing Cybersecurity in Educational Institutions: A Multi-Domain Governance Approach', 'UiTM', 'azlin.ramli.study@gmail.com', '0122417082', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/LXEuyKrg8rnZEWp4mPdIL0JF7tjVSPa6GeKzrsJT.docx', '2025-08-31 08:23:16', '2025-08-31 08:27:08', NULL),
(143, '68b43da6a10cc', 10, 'Adenen Shuhada', 'Binti Abdul Aziz', 'Risk Matrix Framework in Helicopter Water Ditching Incidents: An Analysis of NTSB Data from 2005 to 2022', 'Universiti Pertahanan Nasional Malaysia', 'adenen@upnm.edu.my', '0174379841', 'MY', 'online_author', 550.00, 'paid', 'transfer_bank', 'payment_proofs/EvVNuBgTihabGyQwoICybwczYy6inKfkuebbxa1U.jpg', 'audience_full_papers/UP8b2RT7jhgtYWW8gqMx1kCriKVfUtKVBYd39Igk.docx', '2025-08-31 12:18:46', '2025-09-09 05:21:14', NULL),
(144, '68b4576168bcc', 10, 'Hasmeda Erna', 'Che Hamid', 'Beyond Single Models: An Overview of Ensemble and Hybrid Machine Learning for Improved Flood Prediction', 'UPNM', 'hasmeda@upnm.edu.my', NULL, 'MY', 'online_author', 550.00, 'paid', 'transfer_bank', 'payment_proofs/64stMiBDhYmvC3sJW58ujdKL0SLStyXWerDyb900.jpg', 'audience_full_papers/aTRDI8x4lyAU7sdAZc68MYCcuMkCNMTGoLK1g1g5.docx', '2025-08-31 14:08:33', '2025-09-09 05:19:17', NULL),
(145, '68b465c5b52c0', 10, 'Faizah', 'Shahudin', 'Embracing Adaptability of Business Intelligence: Smart Travel Visualization Using UTAUT Model on Generational Cohorts in Malaysia’s Tourism', 'Xiamen University', 'faizah.shahudin@xmu.edu.my', '+60163584249', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/HnIWGR92ywob72nwbrUNFcFPLioH6exsp5X5rax8.docx', '2025-08-31 15:09:57', '2025-08-31 15:17:56', NULL),
(146, '68b470e1ba143', 10, 'Panca Oktavia', 'Hadi Putra', 'Agile Enterprise Architecture in the Context of Digital Transformation: A Critical Review', 'Universitas Indonesia', 'hadiputra@ui.ac.id', '082170055779', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/9D3wdxggD8kZ9EdDfUujflPtNQaGO6K9mMi22CwP.jpg', 'audience_full_papers/2t0j2RpjFCzS9UBX23TuE3tPiJC7avXZeAM1B4mZ.docx', '2025-08-31 15:57:21', '2025-09-01 01:44:37', NULL),
(147, '68b50896b3457', 10, 'Mike', 'Yuliana', 'Remote Agricultural Visual Diagnostics with Multimodal LLM Using Semantic Transmission in Low-Bandwidth LoRa Environments', 'Politeknik Elektronika Negeri Surabaya', 'mieke@pens.ac.id', '081217464666', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/jOtXMK9sBvF7LVhc96HKzaRP80lETo4MKUzX58GT.jpg', 'audience_full_papers/CLunt6yNUPQ4JjQubCSlgE0XfSoxqHPxj7fggmIr.docx', '2025-09-01 02:44:38', '2025-09-01 03:56:08', NULL),
(148, '68b5110e1b790', 10, 'Maya', 'Agustin', 'Enhancing Research Skills and Student Engagement through Virtual Laboratories in Genetics Learning', 'Faculty of Science, Universitas Negeri Malang', 'mayaagustin11@gmail.com', '082234037474', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/x1iFpDsjFuDIVBaxTpZ7myAD9U6oTPYQfLpR6Nrj.jpg', 'audience_full_papers/mgq3gCRLaq2ILGO4rNp2uRUJKWmmNK6iubxxd0PU.doc', '2025-09-01 03:20:46', '2025-09-01 03:56:19', NULL),
(149, '68b5424f22fb9', 10, 'cheah', 'waishiang', 'AI-Enabled Chatbot for Preserving and Promoting Kuching Cultural Heritage', 'universiti Malaysia Sarawak', 'c.waishiang@gmail.com', '01110989532', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/Ici3FrncRhfOqUKzdg7Ou5JpEftnXrpyisEgUe1J.docx', '2025-09-01 06:50:55', '2025-09-01 06:53:13', NULL),
(150, '68b55a8bdc832', 10, 'TU', 'KOK SIANG', 'Improving the Efficiency of Network Detection and Response (NDR) using Log Consolidation Processing (LCP) for Smart Campus Network', 'Universiti Pertahanan Nasional Malaysia', '3241961@alfateh.upnm.edu.my', '+60163399389', 'MY', 'online_author', 550.00, 'paid', 'transfer_bank', 'payment_proofs/EW3iu3aCOpBUrtYnLOZwXRg9IJK0qkQAQj9EAHtl.png', 'audience_full_papers/netOVNvq7Kikobh9400fJURag3lAaISbz1fFOnu8.docx', '2025-09-01 08:34:19', '2025-09-09 05:18:38', NULL),
(151, '68b563ce89127', 10, 'Adian Fatchur', 'Rochim', 'Natural Language Processing for Research Trend Analysis: Patterns and Evolution Through ScoRBA', 'Department of Computer Engineering, Faculty of Engineering, Universitas Diponegoro, Indonesia', 'adian@ce.undip.ac.id', '0811-251-222', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/P2Ys0nQT7aNHYfAWjA4lNAO88SaOdUgAsQ6pvsxY.png', 'audience_full_papers/CPYHkUT6f3mKLeW3uqkzmgwC5Qy0bR8KTR2u0APj.docx', '2025-09-01 09:13:50', '2025-09-01 12:32:09', NULL),
(152, '68b5a6a958bdf', 10, 'Mohammad Farizshah', 'Ismail Kamil', 'Comprehensive analysis on identifying suitable machine learning methods for predicting quality sheep breed', 'Universiti Pertahanan Nasional Malaysia', 'FARIZGASKIN@GMAIL.COM', '0162780252', 'MY', 'online_author', 550.00, 'paid', 'transfer_bank', 'payment_proofs/hU1w64tcWlXvWj4lOlMveskaxhHr17Ca36jITM4U.jpg', 'audience_full_papers/sq8ssoOBoNwiuhNgebAzaNGUB0IeXwflPOol2dBk.docx', '2025-09-01 13:59:05', '2025-09-09 05:18:16', NULL),
(153, '68b5af6bba32b', 10, 'Mike', 'Yuliana', 'Lightweight Physical Layer Key Generation for V2V and V2I Communication over LoRa: A Hybrid Multibit Quantization with Correlation-Based Algorithm', 'Politeknik Elektronika Negeri Surabaya', 'mikeyuliana811@gmail.com', '081217464666', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/7dGxCN7hqJvrYXF3EWV7OQrPVCKaKICZC0FPBPLM.jpg', 'audience_full_papers/PX211VCFnaOtpIQ2rf6ddtor62ultD6wj4wvgKeB.docx', '2025-09-01 14:36:27', '2025-09-02 01:22:46', NULL),
(154, '68b5bc0c172b5', 10, 'Muhammad Naim', 'Abdullah', 'Utilizing Machine Learning Algorithm to Predict Asthma Exacerbation for Enhancing Asthma Management', 'Universiti Pertahanan Nasional Malaysia', 'm.naim@upnm.edu.my', NULL, 'MY', 'online_author', 550.00, 'paid', 'transfer_bank', 'payment_proofs/jXUHKOgqQEDw5wjiwV3jAwDQzTIzmLcnKXIIwQPY.png', 'audience_full_papers/xQnPSFTfTVrDheKqoP5N2GG8qDMzYl0eI7R5R51j.docx', '2025-09-01 15:30:20', '2025-09-09 05:17:58', NULL),
(155, '68b68a63ce25a', 10, 'Swee-Huay', 'Heng', 'Development of a Post-Quantum Secure Document Encryption Application Using Kyber', 'Multimedia University', 'shheng@mmu.edu.my', NULL, 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/W6ZRDslzwJTHRaTaXIhndRwXcnkU3LX32s0LsnLa.docx', '2025-09-02 06:10:43', '2025-09-03 11:34:20', NULL),
(156, '68b69444df799', 10, 'Su-Cheng', 'Haw', 'Optimizing Recommendation Systems Using Multi-Objective Genetic Algorithms', 'Multimedia University', 'sucheng@mmu.edu.my', '0133952558', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/6uSgEO578fOoYvltMGbNcK4VfTjmAmFZMXCuGSP8.docx', '2025-09-02 06:52:52', '2025-09-02 07:01:29', NULL),
(157, '68b6f312eabde', 10, 'Su-Cheng', 'Haw', 'Enhancing Fraud Detection in Healthcare Through Hybrid Machine Learning Ensembles and Optimization', 'Multimedia University', 'suchenghaw76@gmail.com', '0133952558', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/Wq19OaRGeZDCgoddfS5z505nLpHa6AoWNj4v73jL.docx', '2025-09-02 13:37:22', '2025-09-03 11:30:25', NULL),
(158, '68b829849557a', 10, 'Erna', 'Daniati', 'Integrated DAGCN-Bi-LSTM to model Fairy Tale’s Character Relationships', 'Universitas Negeri Malang', 'erna.daniati.2305349@students.um.ac.id', '081335242202', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/KjMOk5KPfxs5Y6VmqcK1BjdX5LuPFnhl9Gc66rql.jpg', 'audience_full_papers/xvAtZc2ZXlaiJSieWsOItcsbZHJ7Hw9FanCfw3Bt.docx', '2025-09-03 11:41:56', '2025-09-03 12:09:08', NULL),
(159, '68b8482d01810', 10, 'Mochammad', 'Anshori', 'Comparing Various Tree-Based Model Approach to Spot Self-Sabotage Behaviour', 'Institut Teknologi, Sains, dan Kesehatan RS.DR. Soepraoen Kesdam V/BRW', 'moanshori@itsk-soepraoen.ac.id', '6285258346842', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/RSOVXVtfa3KX0Pff4fFafttQsuX95687iAc5Q8mK.jpg', 'audience_full_papers/0qNp7psAP88IJ2QGf8lK9wJRX888zYCdMKJdjofA.docx', '2025-09-03 13:52:45', '2025-09-04 00:24:59', NULL),
(160, '68baa3afb0a58', 10, 'CHUN YANG', 'LAW', 'The Analysis of Succession Planning From a Talent Management Perspectives Using PLS-SEM: Its Influence on Employee Performance with Mediating Role of Organizational Culture in Education', 'National Defence University of Malaysia', '3241902@alfateh.upnm.edu.my', '0182883087', 'MY', 'onsite', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/yA9AkrGU68wK07hlwUapz6wSY2abmJeQXBs7DQIa.jpg', 'audience_full_papers/k8ocDyoatHYUCPhATfT8zZOcBvU52jPwCszwYGap.docx', '2025-09-05 08:47:43', '2025-09-06 04:41:58', '2025-09-06 04:41:58'),
(161, '68faba000350b', 5, 'Sukmawati,', 'Sukmawati', 'Development of a Deep Learning-Based Green-Constitution Digital Education Platform as an Effort to Enhance Environmental  Literacy in Educational Institutions', 'Department of Civics Education, Tadulako University', 'sukmaniezt5@gmail.com', '081310444722', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/RY1wXc6auK7P8ZSB8Aj12jGAlH7Wt69EoQSdeMFc.jpg', 'audience_full_papers/XPI1Y150pLv8IOY3xZhDR91Kb6T82KdNHB7TubKI.docx', '2025-10-23 16:28:00', '2025-10-27 07:27:51', NULL),
(162, '68fae7e1d2d57', 5, 'Yaddarabullah', 'Yaddarabullah', 'Predictive Modeling of Variable Air Volume Terminal Valve Performance Using a Multi-Channel Convolutional Neural Network', 'Universitas Trilogi', 'yaddarabullah@trilogi.ac.id', '+62818749275', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/VnTxEvA80vxuf1OawbzGXmTAUZcdF9ReBL8dSlE3.jpg', 'audience_full_papers/TGstflviIRGv7ouhfT1qyAZrmHTrpqBAjVkNDhu3.docx', '2025-10-23 19:43:45', '2025-10-27 07:27:13', NULL),
(163, '68fae9ab8b945', 5, 'SUNNENG SANDINO', 'BERUTU', 'Enriching Aspect-Based Sentiment Analysis with Degree Adverb and Five-Level Sentiment Scale', 'Department of Informatic, Universitas Kristen Immanuel Yogyakarta', 'sandinoberutu@ukrimuniversity.ac.id', '082223257130', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/2covCzbhol3gVLAvhZG0GaIGqNyCBZClTFA5cPr1.jpg', 'audience_full_papers/DU0Ta535GxFnaWnIWh22zgjAl6qgHGgEGa4aFt2k.docx', '2025-10-23 19:51:23', '2025-10-27 07:25:50', NULL),
(164, '68fb14d6eeda1', 5, 'Komang', 'Oka Saputra', 'MAD-RANSAC Clock Skew Estimator', 'Udayana University', 'okasaputra@unud.ac.id', '08123660060', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/YcNTmbqN31Wl8t33sIF8B2GtWNpkTUE1ytUyIadI.jpg', 'audience_full_papers/h3eaLtAXnIEtcSU9OcJEXnZWVFQxYrlDkkX61O06.docx', '2025-10-23 22:55:34', '2025-10-27 07:20:50', NULL),
(165, '68fb73acb29d5', 5, 'Ghulam Alvi', 'Rahmat', 'A Lightweight Deep Metric Learning Pipeline for State-of-the-Art Face Anti-Spoofing', 'Institut Teknologi Sepuluh Nopember', 'alvirahmatghulam@gmail.com', '082154215205', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/9IYllAfyY0mOlNHV6F3pTMWx8Mw9HXteWEDCMddz.png', 'audience_full_papers/5qWTY7Pir4N2aGeZFzpkOBg9nXEQXQt4QCaFNgMp.docx', '2025-10-24 05:40:12', '2025-10-26 01:08:59', NULL),
(166, '68fb8d624edaa', 5, 'Hera', 'Hastuti', 'Machine Learning Models for Clustering and Validation of Historical Thinking Skills: A Multi-Dimensional Approach', 'Universitas Negeri Padang', 'herahastuti@fis.unp.ac.id', '+6282261075293', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/6pZUbfSGvwP9X34WWydi4TtlaiNwUP7m9tQpwE3D.jpg', 'audience_full_papers/2DWcc7QKyFDZHXkE36Vrz3m5I0epJHHqEa4X8vFn.docx', '2025-10-24 07:29:54', '2025-10-26 01:08:27', NULL),
(167, '68fccb1366c70', 5, 'Muhammad Taufik', 'Dwi Putra', 'Realistic Batik Motif Generation Using The Series of StyleGAN2 and StyleGAN3', 'Indonesia University of Education', 'tdputra@upi.edu', '+6281319619694', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/arOkXOrXbNQ2pVGcJbqjcTPGOBbedz67Ycsnf4bE.jpg', 'audience_full_papers/2yDYYwkK35ypuiSFS7TyAabwlQ0evjldU19cEIvR.doc', '2025-10-25 06:05:23', '2025-10-26 01:07:51', NULL),
(168, '68fed98aadc1b', 5, 'Devi Dwi', 'Purwanto', 'Adaptive Gamification for English Language Learning with Local Historical and Cultural Contexts', 'Universitas Katolik Widya Mandala Surabaya', 'devi.dp@ukwms.ac.id', '+6285745049867', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/x6GXaX44x9Zr2FRShxfc4YMbSSv1xz5yE3rvwVeu.jpg', 'audience_full_papers/qv4TWctlaw1Z67IydsmWi9ILXqU88zCvHhFAWxAm.docx', '2025-10-26 19:31:38', '2025-10-27 07:19:25', NULL),
(169, '68fef0fa94b11', 5, 'Noor Ashikin', 'Mohd Rom', 'Cashless to Seamless: Determinants of Young Adults’ Use of Digital Finance Wallets', 'Assistant Professor', 'ashikinmrom@mmu.edu.my', '0123549817', 'MY', 'onsite', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/3Ag9wsWpozmEUMCF8js9PbA529DdiEJ2urVaO5mX.docx', '2025-10-26 21:11:38', '2025-10-26 21:32:19', NULL),
(170, '68fef84464f8c', 5, 'Mohsen', 'Mohamad Hata', 'Mitigating Alert Fatigue in Security Information and Event Management (SIEM): Ensemble Machine Learning Integration with Elasticsearch, Logstash and Kibana (ELK)', 'Faculty of Computer and Mathematical Sciences, Universiti Teknologi MARA, Shah Alam, Malaysia', 'mohsen@uitm.edu.my', '+60129811334', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/KqoqgyJufyoHMscwMk4ppC0ZrzJzIXqjiKTdjGeM.docx', '2025-10-26 21:42:44', '2025-10-26 22:22:12', NULL),
(171, '68ff30935a740', 5, 'Delfi', 'Eliza', 'Development of a Mobile Role-Playing Game Application to Enhance Scientific and Cultural Literacy in Early Childhood Education', 'Universitas Negeri Padang', 'delfieliza@fip.unp.ac.id', '+6282122071746', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/8FUlWe9YiTow4efEIpP1AJv1U73UPPF1NHbjM3g1.jpg', 'audience_full_papers/oCAveGZnVHl4dFze0FNLt8UB6owE2Qor2n7T54MD.docx', '2025-10-27 01:42:59', '2025-10-27 07:18:39', NULL),
(172, '68ff5a665371f', 5, 'NURZULAIKHA', 'ABDULLAH', 'Image Recognition Model And Quality Detection For Grain', 'Department of Defense Science, Faculty of Defense Science and Technology, University of National Defense of Malaysia, 57000, Kuala Lumpur, Malaysia', 'nurzulaikha@upnm.edu.my', '+60139075519', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/JfrgL8uNoyStr0vulGKX9YeKTCeN1WpvkvZcttU2.png', NULL, '2025-10-27 04:41:26', '2025-10-27 04:41:26', NULL),
(173, '68ff681938ae0', 5, 'Shevti Arbekti', 'Arman', 'Comparative Evaluation of Machine Learning Models for Cervical Cancer Prediction Using TCGA-CESC Clinical and Molecular Data', 'Gunadarma University, Ahmad Dahlan Institute of Technology and Business', 'shevtiarbekti@gmail.com', '083181183345', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/eDHVVN6jqK2kD2LG3iv7d1NkZDkTXtfYdA7jM4bM.jpg', 'audience_full_papers/9YACwLLjalLtJ3s1JbcrQhUes8AxFlNCV3wkcE85.docx', '2025-10-27 05:39:53', '2025-10-27 07:17:17', NULL),
(174, '68ff7e6428181', 5, 'Hoo-Thye', 'Neoh', 'Predicting Marital Satisfaction with Machine Learning: A Maslow-Informed, Cross-Cultural Study', 'Multimedia University (MMU)', 'htneoh@mmu.edu.my', '01118672468', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/WiaK5JTJkKOoP3IDEVKTAwCXUXuEBWmJYBo1arVa.docx', '2025-10-27 07:15:00', '2025-10-27 07:19:21', NULL),
(175, '68ff814085003', 5, 'Shuzlina', 'Abdul-Rahman', 'Analyzing Malaysian\'s Public Viewpoint Towards Electric Vehicles Using Sentiment Analysis', 'Universiti Teknologi MARA Shah Alam, Selangor', 'shuzlina@uitm.edu.my', '0192404120', 'MY', 'online_author', 550.00, 'pending_payment', 'payment_gateway', NULL, 'audience_full_papers/tDQ3tkdvL4H6u67RoQ4y7QqjXpbMHCpVw1jSa0vz.docx', '2025-10-27 07:27:12', '2025-10-27 07:27:12', NULL),
(176, '68ff87164eac7', 5, 'Suwarno', 'Suwarno', 'A Comparative Analysis of the Effectiveness of Hash-Chain-Based and Traditional Logging Audit Trail Systems in Detecting Data Manipulation', 'Universitas Internasional Batam', 'swliang@gmail.com', '085264978288', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/Da7AaP79gsnyoE4AGGuL9pCkb7Im5He1RqGhCLmL.jpg', 'audience_full_papers/c6oPxlASDWLrrD1Nm67jFgyNjcCTVAkR4JlPhcgI.docx', '2025-10-27 07:52:06', '2025-10-28 05:38:03', NULL),
(177, '68ffb680ef160', 5, 'Kiki Ahmad', 'Baihaqi', 'Comparative Study of Simple CNN and U-Net Architectures for NDVI-Based Rice Crop Health Assessment Using Multispectral Imagery', 'Universitas Buana Perjuangan Karawang', 'kikiahmad@ubpkarawang.ac.id', '081281210048', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/F88IHKrb607mElqI6w0kNeLprBOHJTbh77k43s5u.jpg', 'audience_full_papers/PMli45sVl5MShxoisV7cFPuaZo7pmr4LbVSGg9XT.docx', '2025-10-27 11:14:24', '2025-10-28 06:14:46', NULL),
(178, '690035240cd26', 5, 'Rini', 'Sovia', 'Optimization of Text Mining Classification with Hybrid WVCL (Word2Vec-CNN-LSTM) on Indonesian Texts from Twitter', 'Universitas Putra Indonesia YPTK Padang', 'rini_sovia@upiyptk.ac.id', '081267872060', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/LRhKBpWa8A2fhTca30n9bAJ6RqVqmTyjA59dDtw9.jpg', 'audience_full_papers/w6sQxEKqYfLPVXn1BT5GaHWUkuNsuXibRxOlqXlk.docx', '2025-10-27 20:14:44', '2025-10-28 06:11:24', NULL),
(179, '69003aec5c4d1', 5, 'Tri Lathif Mardi', 'Suryanto', 'Optimizing BERT Transformer with Copilot Generated Question– Answer Datasets for Cultural Heritage QA Systems', 'Universitas Pembangunan Nasional Veteran Jawa Timur', 'trilathif.si@upnjatim.ac.id', '085645497900', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/yUkYBNp3DzT1ftUNnAw83DCisvwr9q7IzVunh7uA.jpg', 'audience_full_papers/JbqfX3VcPpSNut1cBUiZd96dCfeXZrFkJSfWRarI.docx', '2025-10-27 20:39:24', '2025-10-28 06:10:45', NULL),
(180, '690048184183f', 5, 'Wahyu', 'Wibowo', 'Analyzing Malaysian’s Public Viewpoint Towards Electric Vehicles Using Sentiment Analysis', 'Institut Teknologi Sepuluh Nopember, Surabaya, Indonesia', 'wahyu_w@statistika.its.ac.id', '081357467722', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/zYA2Mj03dhtH3G953bJYxjk326oznzqF0HTIg4W8.jpg', 'audience_full_papers/a3HxVYB6r9Z7DxSQOAsgOXBx3GA6fXw5PrQ78bBx.docx', '2025-10-27 21:35:36', '2025-10-28 06:10:20', NULL),
(181, '6900487666a5a', 5, 'Made Hanindia Prami', 'Swari', 'Implementation of Transfer Learning for Automatic Summarization in Research Article Synthesis', 'UPN Veteran Jawa Timur', 'madehanindia.fik@upnjatim.ac.id', '081907546050', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/b2YR6u7qSKvHjYiCt23cx4tZq0Zb4r6w5oHOR21R.jpg', 'audience_full_papers/j6MwiqY8hrvCTYUE6uopgyJbKAw2dMWYiWSdXohw.docx', '2025-10-27 21:37:10', '2025-10-28 05:49:30', NULL),
(182, '6900593a8b406', 5, 'SHUZLINA', 'ABDUL RAHMAN', 'Deep Learning for Classifying Rice Crops and Weeds for Precision Agriculture', 'Universiti Teknologi MARA', 'shuzlina@fskm.uitm.edu.my', '0192404120', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, NULL, '2025-10-27 22:48:42', '2025-10-27 22:56:33', NULL),
(183, '690079efded28', 5, 'M. Khairul', 'Anam', 'Optimization of Convolutional Neural Network Architecture for Accurate Banana Ripeness Classification', 'Universitas Samudra', 'khairulanam@unsam.ac.id', '081363769937', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/dyyIVAhDOWwKsyY8ytLSdPPgPupWSP3cfddjgEHC.jpg', 'audience_full_papers/2PWaqVo5WR5D5G5PSlhMpRAG9AvgR4TyxkkHyDFJ.docx', '2025-10-28 01:08:15', '2025-10-28 05:48:58', NULL),
(184, '69007ab694ef2', 5, 'Mochammad Zen', 'Samsono Hadi', 'Comparative Analysis of Deep Learning Methods for Detecting Nutrient Deficiencies in Paddy Leaves', 'Electronic Engineering Polytechnic Institute of Surabaya', 'zenhadi@pens.ac.id', '081292109194', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/PN57xSVwRec1KWoogFyvT9KyZj4i3v2MqMavmU9z.jpg', 'audience_full_papers/bi2AOQJ1b1ww8EOQoiaoO4hEAKEEOvJCTAIV5LWJ.docx', '2025-10-28 01:11:34', '2025-10-28 05:48:23', NULL),
(185, '69008093ac0ff', 5, 'Achmad', 'Murdiono', 'Analysis of MSME Financial Literacy Segmentation Using Clustering Methods', 'Universitas Negeri Malang', 'achmad.murdiono.fe@um.ac.id', '081230066335', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/ggvgXQOMuzk0p2qxOarqBbOssktmA85I48rp3GaF.jpg', 'audience_full_papers/HTt2WGycQnmuRP9jZxpAJAjBRRBeutal4tjT1MKD.docx', '2025-10-28 01:36:35', '2025-10-28 06:15:31', NULL),
(186, '69009b6b6c7b5', 5, 'Afdal', 'Afdal', 'WAFAKO: An Evidence-Based Mobile Application to Model Work–Family Conflict through SEM', 'Universitas Negeri Padang', 'afdal.kons@fip.unp.ac.id', '085263084498', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/duCTw4rqK1pPoCMFBAMJ6ssogg0Tlnq7m5p2vecX.jpg', 'audience_full_papers/E1sBTLbsTNLchTKZl7egbfQ110Nvbvvpjs839S1J.docx', '2025-10-28 03:31:07', '2025-10-28 06:15:57', NULL),
(187, '69009c0d23450', 5, 'Yarmis', 'Syukur', 'A Mobile Application-Based Digital Parenting Framework to Prevent Gaming Disorder: Evidence from SEM Analysis', 'Universitas Negeri Padang', 'yarmissyukur@fip.unp.ac.id', '08126615113', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/32ccFaAfOkY5Oq8h4RZo9INE2kuEmjgJADbdGW7H.jpg', 'audience_full_papers/SNJSXxsyJGxZq3NUG4KopNfeir4VHwd4a7lZwhAV.docx', '2025-10-28 03:33:49', '2025-10-28 06:17:32', NULL),
(188, '69009c9f81f9d', 5, 'Hanif', 'Al Kadri', 'Digital Competence in the Teacher Professional Education Program: A PLS-SEM Study of Self-Efficacy, Institutional Support, and Academic Resilience', 'Universitas Negeri Padang', 'hanifalkadri@fip.unp.ac.id', '081363130999', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/nsfsEKzCAQtZTQtnU8rG9T7a1JBNNMxiYtJuBVMY.jpg', 'audience_full_papers/p2fWh232LSzNx3QoekvzdCo6EtYnpllPxuaQI0Oy.docx', '2025-10-28 03:36:15', '2025-10-28 06:21:18', NULL),
(189, '69009d285ac67', 5, 'Frischa Meivilona', 'Yendi', 'A Technology-Based Healthy Lifestyle Framework to Reduce Hopelessness among Final-Year University Students: Evidence from PLS-SEM Analysis', 'Universitas Negeri Padang', 'frischa@fip.unp.ac.id', '085266279821', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/AtKVCovYZ3kbWwUqpFov3S8JisHCXFxrJdpT1KWq.jpg', 'audience_full_papers/6URtzlRWjv6X0vCnsOMMP6psSoin43OCASlItNCc.docx', '2025-10-28 03:38:32', '2025-10-28 06:25:41', NULL),
(190, '69009db67c733', 5, 'Yeni', 'Erita', 'Improving Elementary School Learning through Augmented Reality and Deep Learning: A Technology-Based Framework', 'Universitas Negeri Padang', 'yenierita@fip.unp.ac.id', '085263051962', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/fNDxcbbuOQHDNv4k5kYKEsHe07YAzndt5NhkFd4A.jpg', 'audience_full_papers/ztNPJskDSWtgxn1loKpRmhkxKpHbx2K1jvZnPQs5.docx', '2025-10-28 03:40:54', '2025-10-28 06:26:17', NULL),
(191, '69009e3c21e1e', 5, 'Nurhastuti', 'Nurhastuti', 'Relationship Intelligent Assessment Platform for Digital Learning Analytics: CIBI Application in Measuring Creative Thinking, Leadership, and Academic Attitude Relationships', 'Universitas Negeri Padang', 'nurhastuti@fip.unp.ac.id', '082120060068', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/1yUBrNrfMvXFkfTUwjdsXtlklYudRYpjXWOqFV7B.jpg', 'audience_full_papers/MPDDZMIcIZLNpdfRE013Yw5Rm1mtnpTRvyT0nrYK.docx', '2025-10-28 03:43:08', '2025-10-28 06:27:05', NULL),
(192, '6900a4c45c8d8', 5, 'Aulia', 'Siti Aisjah', 'Clustering Indonesian Provinces Based on Multidimensional Food Security Indicators Using t-SNE and DBSCAN', 'Institut Teknologi Sepuluh Nopember', 'aulisa@its.ac.id', '6282228940099', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/dCQlUSOCvMUamTXwUXjDCdCMsPdyYya5nU7if3SA.jpg', 'audience_full_papers/eK7U2Y7ocsM57KddYgoZ3xnex76ImmDwec0ew6zk.docx', '2025-10-28 04:11:00', '2025-10-28 06:27:28', NULL),
(193, '6900b8e59da96', 5, 'Asrul', 'Huda', 'Developing Augmented Reality-Based Smart Learning Media and Examining Its Validity and Practicality on Basic Photography Elements', 'Universitas Negeri Padang', 'asrulhuda@ft.unp.ac.id', '+6281363777678', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/M33do2OhfTqV63tVQRiWK17c20Ac6D450nHyb0YH.jpg', 'audience_full_papers/2LQuZpSWGrXoCPSTqckOJNqOZ5L8dMPLSMe4XUsk.docx', '2025-10-28 05:36:53', '2025-10-28 06:29:58', NULL),
(194, '6900bacfc00c2', 5, 'Asrul', 'Huda', 'Design and Development of a Web-Based Library Chatbot with Integration of a Local Database (MySQL) and Artificial Intelligence Services (OpenAI API)', 'Universitas Negeri Padang', 'asrulhuda@gmail.com', '+6281363777678', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/qVWCApKmr818FYZTsM89RGoGVLP4CSHdkCqN1XXv.jpg', 'audience_full_papers/SHv7MsO7KMBuVwb5FLkCd3eu8VvqS2hUJn6M8Dab.docx', '2025-10-28 05:45:03', '2025-10-28 06:30:11', NULL),
(195, '6900cc1c818fc', 5, 'Swee-Huay', 'Heng', 'Digital Signature Library with Dilithium', 'Multimedia University', 'shheng@mmu.edu.my', '0136309713', 'MY', 'online_author', 550.00, 'paid', 'payment_gateway', NULL, 'audience_full_papers/yPJIR6U6soK3E0sgCiDaWiW2JN0yhkGVTUilIY4A.docx', '2025-10-28 06:58:52', '2025-10-28 07:09:36', NULL),
(196, '6900cdbe25ed9', 5, 'Sarah', 'Bibi', 'Adaptive MOOCs as an Innovative Online Learning Model for Sustainable Professional Development of Rural Teachers: A Quasi-Experimental Study', 'Politeknik Negeri Pontianak', 'sarahbibi@polnep.ac.id', '+6285252671250', 'ID', 'onsite', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/8ldjI00J1r01a4qa82PIsOeT6pIlhbSUW8MvySxS.jpg', 'audience_full_papers/8ghCmTS4ghvtC4ADIWqVhHDdqiFDoPDzHTm3DkBM.docx', '2025-10-28 07:05:50', '2025-10-28 18:12:14', NULL),
(197, '6900dd7a44c0e', 5, 'Galih Wasis', 'Wicaksono', 'Leveraging Retrieval-Augmented Generation to Support Legal Question Answering: Evidence from Indonesian Human Trafficking Court Decisions', 'Universitas Muhammadiyah Malang', 'galih.w.w@umm.ac.id', '082142582102', 'ID', 'online_author', 8500000.00, 'paid', 'transfer_bank', 'payment_proofs/rmyBJhlgw7ZRllPiFBrd8Wv7rzkCioE0nhOy7KOC.jpg', 'audience_full_papers/1SoYqqXdzcd0HYCBFNVoqjnizV2B6mafk052Mm1B.docx', '2025-10-28 08:12:58', '2025-10-28 18:14:18', NULL),
(198, '6902f90a5798f', 5, 'Brigida Intan', 'Printina', 'The Effectiveness of Diorama Media and Digital Archives of Aboriginal Tribes Loaded with Design Thinking to Strengthen the National Identity of History Learners', 'History Education Study Program, Faculty of Teacher Training and Education, Universitas Sanata Dharma, Yogyakarta, Indonesia,', 'brigidaintan@usd.ac.id', '+6285220051283', 'ID', 'online_author', 8500000.00, 'pending_payment', 'transfer_bank', 'payment_proofs/m2qdoVaH77Hkm1jq4SiBTkawmita3Oi6IVrKaAmj.jpg', NULL, '2025-10-29 22:35:06', '2025-11-01 06:07:28', '2025-11-01 06:07:28'),
(199, '690769c92a959', 5, 'Husna Sarirah', 'Husin', 'A Hybrid Approach For AI-Generated Image Detection System Integrating Neural Network and Digital Forensic Error Level Analysis', 'Taylor\'s University', 'husna.husin@taylors.edu.my', '0137515014', 'MY', 'online_author', 550.00, 'pending_payment', 'transfer_bank', 'payment_proofs/BkB0qCbP4inoEgzBkxDGzi9HNoiwS56JmwqZgNbZ.jpg', 'audience_full_papers/sc4ONRkVVYoTbOv5TfOXqn8O4nNg1Yjy7lVgtBOD.docx', '2025-11-02 07:25:13', '2025-11-02 07:25:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conferences`
--

CREATE TABLE `conferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_id` varchar(100) NOT NULL,
  `name` varchar(255) NOT NULL,
  `initial` varchar(255) DEFAULT NULL,
  `cover_poster_path` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `online_fee_usd` decimal(10,2) NOT NULL,
  `online_fee` decimal(10,2) NOT NULL,
  `onsite_fee_usd` decimal(10,2) NOT NULL,
  `onsite_fee` decimal(10,2) NOT NULL,
  `participant_fee_usd` decimal(10,2) NOT NULL,
  `participant_fee` decimal(10,2) NOT NULL,
  `certificate_template_path` varchar(255) DEFAULT NULL,
  `certificate_template_position` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `conferences`
--

INSERT INTO `conferences` (`id`, `public_id`, `name`, `initial`, `cover_poster_path`, `date`, `city`, `country`, `year`, `online_fee_usd`, `online_fee`, `onsite_fee_usd`, `onsite_fee`, `participant_fee_usd`, `participant_fee`, `certificate_template_path`, `certificate_template_position`, `created_at`, `updated_at`) VALUES
(5, '5ece4797eaf5e', 'The 1st International Conference on Digital Evolution and Innovation', 'InCode 2025', 'conference_posters/BbtjiCARgYMPNONZwAlhQkabyTdKlr7NlsuQw1RK.png', '2025-11-08', 'Langkawi', 'Malaysia', 2025, 550.00, 8500000.00, 550.00, 8500000.00, 100.00, 1500000.00, 'conference_certificate/bP0zGIHxzgOsQdjNEowYCcXbBTVyZxshKsTRajHE.png', '{\"positions\":\"{\\\"name\\\":{\\\"x\\\":418,\\\"y\\\":267.5,\\\"size\\\":24,\\\"width\\\":400,\\\"color\\\":\\\"#000000\\\",\\\"align\\\":\\\"center\\\",\\\"text\\\":\\\"NAMA PESERTA\\\"}}\"}', '2025-06-10 11:43:36', '2025-10-26 01:00:27'),
(10, '68aadfa2f0e88', 'The 3rd 2025 Software & Technologies, Visual Informatics & Applications (SOTVIA) Conference', 'SOTVIA2025', 'conference_posters/XIT5OWDJLzFLMZZeohe1rDBIXp2cBDxnFP2jnNAT.png', '2025-09-09', 'Nanjing', 'China', 2025, 550.00, 8500000.00, 550.00, 8500000.00, 100.00, 1500000.00, 'conference_certificate/WgQYGXkmudjUVB8Amzhb1UVdegR5eQ3ZaSVckNaG.jpg', '{\"positions\":\"{\\\"name\\\":{\\\"x\\\":422,\\\"y\\\":296.5,\\\"size\\\":20,\\\"width\\\":400,\\\"color\\\":\\\"#000000\\\",\\\"align\\\":\\\"center\\\",\\\"text\\\":\\\"NAMA PESERTA\\\"},\\\"paper_title\\\":{\\\"x\\\":420,\\\"y\\\":329.5,\\\"size\\\":14,\\\"width\\\":700,\\\"color\\\":\\\"#000000\\\",\\\"align\\\":\\\"center\\\",\\\"text\\\":\\\"PAPER_TITLE\\\"}}\"}', '2025-08-24 09:47:14', '2025-10-10 23:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_history`
--

CREATE TABLE `invoice_history` (
  `id` varchar(100) NOT NULL,
  `audience_id` bigint(20) UNSIGNED NOT NULL,
  `snap_token` varchar(225) DEFAULT NULL,
  `capture_id` varchar(225) DEFAULT NULL COMMENT 'for paypal payment',
  `payment_method` varchar(50) NOT NULL COMMENT 'paypal, midtrans',
  `redirect_url` varchar(225) DEFAULT NULL,
  `expired_at` int(11) DEFAULT NULL,
  `amount` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_history`
--

INSERT INTO `invoice_history` (`id`, `audience_id`, `snap_token`, `capture_id`, `payment_method`, `redirect_url`, `expired_at`, `amount`, `status`, `created_at`, `updated_at`) VALUES
('ORDER-6896a46c7f746', 86, '6e30a411-ff42-4098-83cd-5a578265be45', NULL, 'midtrans', NULL, 1754789356, 555000, 'pending', '2025-08-09 01:29:16', '2025-08-09 01:29:16'),
('ORDER-68a072c9d0b98', 89, '1GL02004UM3311432', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=1GL02004UM3311432', 1755432013, 555000, 'paid', '2025-08-16 12:00:13', '2025-08-16 12:00:31'),
('ORDER-68a1cc6b38ca6', 90, 'ec58124f-1dac-4472-8a26-539c3e10fb6a', NULL, 'midtrans', NULL, 1755520491, 555000, 'expire', '2025-08-17 12:34:51', '2025-08-18 12:38:04'),
('ORDER-68a1cd31eaea1', 91, 'c59b30cf-dadc-4b60-9563-af854f432223', NULL, 'midtrans', NULL, 1755520690, 51000, 'settlement', '2025-08-17 12:38:10', '2025-08-17 13:01:34'),
('ORDER-68aae09b1fc20', 96, '55cba266-7e39-4a06-885e-b2e7e81b8f3b', NULL, 'midtrans', NULL, 1756115483, 8500000, 'expire', '2025-08-24 09:51:23', '2025-08-25 09:52:55'),
('ORDER-68aaf81cb6fe8', 97, '74f5f353-1816-4541-9685-f0c5a5040e39', NULL, '', NULL, 1756440136, 123000, '', '2025-08-24 11:31:40', '2025-08-25 11:33:46'),
('ORDER-68ad9f6d40f6b', 100, '8fe36759-c06f-4084-92fc-b0512ffae0fe', NULL, 'midtrans', NULL, 1756295405, 1500000, 'pending', '2025-08-26 11:50:05', '2025-08-26 11:50:05'),
('ORDER-68afa79bf0b9a', 106, 'f7599f08-1bb6-4d2d-a110-8fad7bc52938', NULL, 'midtrans', NULL, 1756428572, 8500000, 'settlement', '2025-08-28 00:49:32', '2025-08-28 00:50:46'),
('ORDER-68b12b6e4a8b2', 113, '7SL90869PW0266529', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=7SL90869PW0266529', 1756527855, 1500000, 'pending', '2025-08-29 04:24:15', '2025-08-29 04:24:15'),
('ORDER-68b13615838a5', 117, '2SU68870J0417531V', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=2SU68870J0417531V', 1756530582, 550, 'paid', '2025-08-29 05:09:42', '2025-08-29 05:22:17'),
('ORDER-68b154291368d', 119, '1f479577-7c23-429a-8e15-d09eac13b4ea', NULL, 'midtrans', NULL, 1756538281, 8500000, 'settlement', '2025-08-29 07:18:01', '2025-08-29 07:23:09'),
('ORDER-68b1b39af271d', 122, '9F3576190H6733228', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=9F3576190H6733228', 1756562717, 550, 'pending', '2025-08-29 14:05:17', '2025-08-29 14:05:17'),
('ORDER-68b1b39c263cd', 122, '7RK93465HR532505M', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=7RK93465HR532505M', 1756562716, 550, 'pending', '2025-08-29 14:05:16', '2025-08-29 14:05:16'),
('ORDER-68b1b39cd70e5', 122, '18890139607711019', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=18890139607711019', 1756562717, 550, 'pending', '2025-08-29 14:05:17', '2025-08-29 14:05:17'),
('ORDER-68b1b53875b4b', 123, '2UM856094B6022324', '5MK73061H89041910', 'paypal', 'https://www.paypal.com/checkoutnow?token=2UM856094B6022324', 1756563129, 550, 'paid', '2025-08-29 14:12:09', '2025-08-30 15:50:59'),
('ORDER-68b2c2b076b36', 135, '50226b77-dcf4-4402-9b7b-9fa1ee46502e', NULL, 'midtrans', NULL, 1756632112, 8500000, 'expire', '2025-08-30 09:21:52', '2025-08-31 09:24:49'),
('ORDER-68b4067d9c78b', 142, '7WU88110UW614000E', '6TJ59759E8286304L', 'paypal', 'https://www.paypal.com/checkoutnow?token=7WU88110UW614000E', 1756715006, 550, 'paid', '2025-08-31 08:23:26', '2025-08-31 08:27:08'),
('ORDER-68b465e635dea', 145, '8X0949222F4858350', '1PR25931YE872252N', 'paypal', 'https://www.paypal.com/checkoutnow?token=8X0949222F4858350', 1756739431, 550, 'paid', '2025-08-31 15:10:31', '2025-08-31 15:17:56'),
('ORDER-68b542790a80c', 149, '7KH966082S8542542', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=7KH966082S8542542', 1756795898, 550, 'pending', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
('ORDER-68b5427921aa6', 149, '6NF89949KN3181351', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=6NF89949KN3181351', 1756795898, 550, 'pending', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
('ORDER-68b54279396f1', 149, '4P605616U56750214', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=4P605616U56750214', 1756795898, 550, 'pending', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
('ORDER-68b54279543ec', 149, '38393290CD694182N', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=38393290CD694182N', 1756795898, 550, 'pending', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
('ORDER-68b5427956f8a', 149, '01C71363WU0104312', '94P86059M5329750N', 'paypal', 'https://www.paypal.com/checkoutnow?token=01C71363WU0104312', 1756795898, 550, 'paid', '2025-09-01 06:51:38', '2025-09-01 06:53:13'),
('ORDER-68b5427974177', 149, '0RY026413W203143S', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=0RY026413W203143S', 1756795898, 550, 'pending', '2025-09-01 06:51:38', '2025-09-01 06:51:38'),
('ORDER-68b68baf548da', 155, '7VB005232N634515V', '8VX25247JR942102K', 'paypal', 'https://www.paypal.com/checkoutnow?token=7VB005232N634515V', 1756880176, 550, 'paid', '2025-09-02 06:16:16', '2025-09-03 11:34:20'),
('ORDER-68b69452dddcb', 156, '5LW98467292906102', '73E61013DY4399917', 'paypal', 'https://www.paypal.com/checkoutnow?token=5LW98467292906102', 1756882387, 550, 'paid', '2025-09-02 06:53:07', '2025-09-02 07:01:29'),
('ORDER-68b6f322b487b', 157, '49K19726VP7312548', '71B82969J7007983R', 'paypal', 'https://www.paypal.com/checkoutnow?token=49K19726VP7312548', 1756906660, 550, 'paid', '2025-09-02 13:37:40', '2025-09-03 11:30:25'),
('ORDER-68fef38f813e9', 169, '1XK163913K847391H', '6HA96661B7933582X', 'paypal', 'https://www.paypal.com/checkoutnow?token=1XK163913K847391H', 1761625362, 550, 'paid', '2025-10-26 21:22:42', '2025-10-26 21:32:19'),
('ORDER-68fef8ddadcb6', 170, '2FX61831VA233803X', '38P24262MA418542G', 'paypal', 'https://www.paypal.com/checkoutnow?token=2FX61831VA233803X', 1761626718, 550, 'paid', '2025-10-26 21:45:18', '2025-10-26 22:22:12'),
('ORDER-68ff7f58466c5', 174, '8M3424740X671154P', '3RW12188TT619901D', 'paypal', 'https://www.paypal.com/checkoutnow?token=8M3424740X671154P', 1761661145, 550, 'paid', '2025-10-27 07:19:05', '2025-10-27 07:19:21'),
('ORDER-68ff8158509ed', 175, '9LA179889C4652548', NULL, 'paypal', 'https://www.paypal.com/checkoutnow?token=9LA179889C4652548', 1761661657, 550, 'pending', '2025-10-27 07:27:37', '2025-10-27 07:27:37'),
('ORDER-6900596018409', 182, '25E51524MN581623N', '79B9646967354072S', 'paypal', 'https://www.paypal.com/checkoutnow?token=25E51524MN581623N', 1761716961, 550, 'paid', '2025-10-27 22:49:21', '2025-10-27 22:56:33'),
('ORDER-6900cc80bcf43', 195, '717620086R714350L', '9DD81024AB911002K', 'paypal', 'https://www.paypal.com/checkoutnow?token=717620086R714350L', 1761746436, 550, 'paid', '2025-10-28 07:00:36', '2025-10-28 07:09:36');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `key_notes`
--

CREATE TABLE `key_notes` (
  `id` int(11) NOT NULL,
  `audience_id` bigint(20) UNSIGNED NOT NULL,
  `name_of_participant` varchar(225) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `key_notes`
--

INSERT INTO `key_notes` (`id`, `audience_id`, `name_of_participant`, `feedback`, `created_at`, `updated_at`) VALUES
(2, 142, 'AZLIN RAMLI', 'OK', '2025-09-09 01:35:18', '2025-09-09 01:35:18'),
(3, 110, 'Duman Care Khrisne', 'Prof. Haw is explained very well about recommender system, Thank You', '2025-09-09 01:39:37', '2025-09-09 01:39:37'),
(4, 108, 'I Wayan Santiyasa', 'all the topics presented gave me new insights in the field of informatics', '2025-09-09 01:39:37', '2025-09-09 01:39:37'),
(5, 107, 'Risqy Siwi Pradini', 'Thank you, Professor Haw, for your excellent and insightful presentation on recommendation systems. I hope the knowledge you\'ve shared will be beneficial to me in my research and practice in this field.', '2025-09-09 01:40:45', '2025-09-09 01:40:45'),
(6, 117, 'Vishal R', 'Informative and Insightful.', '2025-09-09 01:42:39', '2025-09-09 01:42:39'),
(7, 156, 'Su-Cheng Haw', 'Thank you for inviting me as the keynote.  Looking forward to SOTVIA 2026', '2025-09-09 01:43:49', '2025-09-09 01:43:49'),
(8, 119, 'Widyadi Setiawan', '-', '2025-09-09 01:46:33', '2025-09-09 01:46:33'),
(9, 149, 'Wai Shiang Cheah', 'Nil', '2025-09-09 01:52:28', '2025-09-09 01:52:28'),
(10, 109, 'Putri Taqwa Prasetyaningrum', 'very good', '2025-09-09 01:52:35', '2025-09-09 01:52:35'),
(11, 155, 'Syh-Yuan Tan', 'Good sharing, thanks!', '2025-09-09 01:52:57', '2025-09-09 01:52:57'),
(12, 106, 'Ninuk Wiliani', 'Very Good', '2025-09-09 01:53:33', '2025-09-09 01:53:33'),
(13, 159, 'Mochammad Anshori', 'Insughfull', '2025-09-09 01:55:25', '2025-09-09 01:55:25'),
(14, 103, 'M. Khairul Anam', 'good', '2025-09-09 01:57:55', '2025-09-09 01:57:55'),
(15, 130, 'Agus Eko Minarno', 'Every time it is organized, the conference proves to be the best platform for knowledge and collaboration. Thank you to the committee, good job.', '2025-09-09 01:59:41', '2025-09-09 01:59:41'),
(16, 123, 'CHEN CHUEN LEE', 'The speakers delivered valuable perspectives and research.', '2025-09-09 02:15:03', '2025-09-09 02:15:03'),
(17, 104, 'Denar Regata Akbi', 'The topics presented by the keynote speaker were very interesting', '2025-09-09 02:18:24', '2025-09-09 02:18:24'),
(18, 148, 'Maya Agustin', 'This material provides a clear and insightful overview of recommender systems in e-commerce, highlighting key challenges such as the cold-start item, user problems, and data sparsity. The focus on hybrid techniques and their application in retail and e-commerce is highly relevant. However, including real-world case studies or deeper explanations of specific hybrid algorithms would enhance understanding. Addressing current solutions to the mentioned challenges would provide more practical value. Overall, it is a comprehensive and informative presentation.', '2025-09-09 02:22:12', '2025-09-09 02:22:12'),
(19, 147, 'Mike Yuliana', 'it\'s nice conference', '2025-09-09 02:24:53', '2025-09-09 02:24:53'),
(20, 134, 'Ari Muzakir', 'the activity was conducted effectively and left a positive impression', '2025-09-09 02:29:09', '2025-09-09 02:29:09'),
(21, 137, 'RUHAIDAH ABU NOR', '-', '2025-09-09 02:29:38', '2025-09-09 02:29:38'),
(22, 114, 'Widi Sarinastiti', 'Thankyou', '2025-09-09 02:32:18', '2025-09-09 02:32:18'),
(23, 127, 'A\'in Hazwani Ahmad Rizal', 'attended the session', '2025-09-09 02:46:08', '2025-09-09 02:46:08'),
(24, 128, 'Mukhtar Nazifi', 'Best wishes, thank you.', '2025-09-09 02:46:30', '2025-09-09 02:46:30'),
(25, 133, 'Awang Hendrianto Pratomo', 'its ok. The sound in main room is not clear', '2025-09-09 03:00:33', '2025-09-09 03:00:33'),
(26, 151, 'Adian Fatchur Rochim', 'inspiring', '2025-09-09 03:02:09', '2025-09-09 03:02:09'),
(27, 143, 'Adenen Shuhada binti Abdul Aziz', 'good', '2025-09-09 03:04:16', '2025-09-09 03:04:16'),
(28, 158, 'Erna Daniati', 'We hope this conference always is held annualy with context according to hot issue in Software & Technologies, Visual Informatics & Applications and high contribution to knowledge.', '2025-09-09 03:06:25', '2025-09-09 03:06:25'),
(29, 132, 'Nuzul Hidayat', 'nice presentation', '2025-09-09 03:13:20', '2025-09-09 03:13:20'),
(30, 139, 'Roziyani Rawi', 'great sharing', '2025-09-09 03:35:08', '2025-09-09 03:35:08'),
(31, 116, 'Dewa Made Wiharta', 'Topics are interesting', '2025-09-09 04:07:56', '2025-09-09 04:07:56'),
(32, 136, 'Dhio Saputra', 'Thank you for the opportunity to attend this conference, I hope the event is successful and beneficial.\r\ncan you issue the acceptance letter by this week as we need to claim for the fees that has been paid for this publication. Tq', '2025-09-13 09:07:42', '2025-09-13 09:07:42');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_10_175259_add_initial_cover_date_to_conferences_table', 1),
(5, '2025_06_10_181226_create_conferences_table', 2),
(6, '2025_06_10_191717_create_registrations_table', 3),
(7, '2025_06_11_025208_create_audiences_table', 4),
(8, '2025_06_11_085805_add_payment_method_proof_to_audiences_table', 5),
(9, '2025_08_01_161925_create_invoice_histories_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `parallel_sessions`
--

CREATE TABLE `parallel_sessions` (
  `id` int(11) NOT NULL,
  `audience_id` bigint(20) UNSIGNED NOT NULL,
  `name_of_presenter` varchar(225) NOT NULL,
  `room_id` int(11) NOT NULL,
  `paper_title` varchar(225) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parallel_sessions`
--

INSERT INTO `parallel_sessions` (`id`, `audience_id`, `name_of_presenter`, `room_id`, `paper_title`, `created_at`, `updated_at`) VALUES
(2, 159, 'Mochammad Anshori', 13, 'Comparing Various Tree-Based Model Approach to Spot Self-Sabotage Behaviour', '2025-09-09 05:04:17', '2025-09-09 05:04:17'),
(3, 119, 'Widyadi Setiawan', 13, 'Accelerated Detection of Vessel Loitering Behavior Using GPU-Based Processing of Automatic Identification System Data', '2025-09-09 05:04:21', '2025-09-09 05:04:21'),
(4, 155, 'Syh-Yuan Tan', 12, 'Development of a Post-Quantum Secure Document Encryption Application Using Kyber', '2025-09-09 05:08:40', '2025-09-09 05:08:40'),
(5, 107, 'Risqy Siwi Pradini', 12, 'Optimizing the Performance of K-Nearest Neighbors for Diabetes Classification in Women Through GridSearchCV-Based Hyperparameter Tuning', '2025-09-09 05:08:52', '2025-09-09 05:08:52'),
(6, 109, 'Putri Taqwa Prasetyaningrum', 12, 'Adaptive Gamified Virtual Reality Framework to Enhance Student Engagement and Accessibility in Educational Counseling', '2025-09-09 05:11:43', '2025-09-09 05:11:43'),
(7, 108, 'I Wayan Santiyasa', 12, 'A Data-Driven Multi-Sensor Detection for Machine Learning Recommendation: Case Study of Controlled Agriculture on Volcanic Land', '2025-09-09 05:15:38', '2025-09-09 05:15:38'),
(8, 147, 'Hendri Darmawan', 13, 'Remote Agricultural Visual Diagnostics with Multimodal LLM Using Semantic Transmission in Low-Bandwidth LoRa Environments', '2025-09-09 05:19:43', '2025-09-09 05:19:43'),
(9, 140, 'Arif Basofi', 12, 'A Mobile-Based Approach to Library Book Cataloguing: Leveraging OCR, AI, and Integrated RPA', '2025-09-09 05:21:47', '2025-09-09 05:21:47'),
(10, 143, 'Adenen Shuhada Abdul Aziz', 13, 'Risk Matrix Framework in Helicopter Water Ditching Incidents: An Analysis of NTSB Data from 2005 to 2022', '2025-09-09 05:23:27', '2025-09-09 05:23:27'),
(11, 106, 'Ninuk Wiliani', 9, 'Optimizing Convolutional Neural Network for Surface Defect Detection on Solar Panels in Smart Farming Applications', '2025-09-09 05:24:25', '2025-09-09 05:24:25'),
(12, 110, 'Duman Care Khrisne', 11, 'Traffic Signal Control via Proximal Policy Optimization with Reward Shaping to Minimize Waiting Time and Violations', '2025-09-09 05:25:25', '2025-09-09 05:25:25'),
(13, 153, 'Habib Hammam Kurniawan', 11, 'Lightweight Physical Layer Key Generation for V2V and V2I Communication over  LoRa: A Hybrid Multibit Quantization with Correlation-Based Algorithm', '2025-09-09 05:26:27', '2025-09-09 05:26:27'),
(14, 130, 'Agus Eko', 11, 'Enhancing BatikGAN through ESRGAN-Based Discriminator forHigh-Resolution Batik Pattern Synthesis', '2025-09-09 05:32:08', '2025-09-09 05:32:08'),
(15, 133, 'Awang Hendrianto Pratomo', 11, 'Application of EfficientNet Architecture for Geophysical Event Classification Using Transfer Learning Convolutional Neural Network Method', '2025-09-09 05:35:54', '2025-09-09 05:35:54'),
(16, 148, 'Maya Agustin', 12, 'Enhancing Research Skills and Student Engagement through Virtual Laboratories in Genetics Learning', '2025-09-09 05:37:34', '2025-09-09 05:37:34'),
(17, 139, 'Roziyani Rawi', 9, 'Performance Evaluation of Best Effort QoS in Multi-User Wireless Networks Using NetSim', '2025-09-09 05:41:56', '2025-09-09 05:41:56'),
(18, 149, 'Wai Shiang Cheah', 12, 'AI-Enabled Chatbot for Preserving and Promoting Kuching Cultural Heritage', '2025-09-09 05:42:03', '2025-09-09 05:42:03'),
(19, 132, 'Nuzul Hidayat', 11, 'Arduino-Based Intelligent Control of Condensate Spraying on Condenser to Enhance COP in Automotive Air Conditioning System', '2025-09-09 05:42:31', '2025-09-09 05:42:31'),
(20, 123, 'CHEN CHUEN LEE', 9, 'Exploring Gaps in Cyber Resilience: A Systematic Literature Review of Human Factors and AI', '2025-09-09 05:43:42', '2025-09-09 05:43:42'),
(21, 126, 'MOHD FAIZAL MUSTAFA', 9, 'Enhancing Cybersecurity in Campus Networks: Integration of AI in Log Consolidation Processing (LCP) Framework for Network Threat Detection and Mitigation', '2025-09-09 05:43:58', '2025-09-09 05:43:58'),
(22, 114, 'Widi Sarinastiti', 13, 'Analyzing the Effect of Age and Experience on Usability Performance', '2025-09-09 05:47:32', '2025-09-09 05:47:32'),
(23, 151, 'Lis Setyowati', 13, 'Natural Language Processing for Research Trend Analysis: Patterns and Evolution Through ScoRBA', '2025-09-09 05:51:10', '2025-09-09 05:51:10'),
(24, 150, 'TU KOK SIANG', 9, 'Improving the Efficiency of Network Detection and Response (NDR) using Log Consolidation Processing (LCP) for Smart Campus Network', '2025-09-09 06:12:14', '2025-09-09 06:12:14'),
(25, 127, 'A\'in Hazwani Ahmad Rizal', 9, 'V2X Communication in 5G Networks and Beyond: Security Challenges and the Evolution of Cyber Threat Intelligence Frameworks', '2025-09-09 06:24:37', '2025-09-09 06:24:37'),
(26, 120, 'Ruzanna Mat Jusoh', 13, 'Inappropriate Prescribing in Older Diabetic Patients: A Decision Analysis Approach', '2025-09-09 06:28:07', '2025-09-09 06:28:07'),
(27, 104, 'Denar Regata Akbi', 11, 'Batik Motif Classification using Textual Inversion in Low-Data Settings', '2025-09-09 07:34:51', '2025-09-09 07:34:51'),
(28, 137, 'RUHAIDAH ABU NOR', 10, 'Enhancing the Programming Syntax Connection Between Programming Language and Database System (PHP and Sybase) While Maintaining the System', '2025-09-09 08:04:34', '2025-09-09 08:04:34'),
(29, 124, 'Associate Binti Ramli', 20, 'Classification of Surface Defects in Solar Panels Using GLCM Texture Features with KNN and Naive Bayes Classifiers', '2025-09-13 09:01:50', '2025-09-13 09:01:50'),
(30, 131, 'ANUSUYAH SUBBARAO', 19, 'Beyond Screens: How Gen Z\'s Digital-Native Mindset is Reshaping Educational Design', '2025-09-13 09:02:46', '2025-09-13 09:02:46'),
(31, 145, 'Zihan Gao', 19, 'Embracing Adaptability of Business Intelligence: Smart Travel Visualization Using UTAUT Model on Generational Cohorts in Malaysia’s Tourism', '2025-09-13 09:05:08', '2025-09-13 09:05:08'),
(32, 136, 'Dhio Saputra', 21, 'Real-Time Shuttlecock Detection and Scoring System in Badminton Using the Morphological Region of Development (MrD) Algorithm', '2025-09-13 09:09:54', '2025-09-13 09:09:54'),
(33, 122, 'CHUN YANG LAW', 19, 'The Analysis of Succession Planning From a Talent Management Perspectives Using PLS-SEM: Its Influence on Employee Performance with Mediating Role of Organizational Culture in Education', '2025-09-13 09:27:14', '2025-09-13 09:27:14'),
(34, 142, 'AZLIN RAMLI', 20, 'Developing a Holistic ICT Security Framework for Enhancing Cybersecurity in Educational Institutions: A Multi-Domain Governance Approach', '2025-09-13 10:06:11', '2025-09-13 10:06:11'),
(35, 118, 'Nor Fyadzillah Mohd Taha', 19, 'A Systematic Review of Adaptive ABC Inventory Models in Food Manufacturing: Integrating MCDM, IoT, and Machine Learning', '2025-09-18 02:00:13', '2025-09-18 02:00:13'),
(36, 141, 'Mohd Sidek Fadhil Mohd Yunus', 19, 'DevSecOps in Practice: Where the Framework Falls Short?', '2025-09-19 05:24:39', '2025-09-19 05:24:39'),
(37, 121, 'Muhamad Lazim Talib', 20, 'Digital Fingerprints: Tracing Image Provenance from Sensor  Pattern Noise to Generative Model Artefacts', '2025-09-28 23:32:12', '2025-09-28 23:32:12'),
(38, 103, 'M. Khairul Anam', 24, 'Hybrid CNN-BiLSTM-Machine Learning Architecture (HACBML) for Enhancing Anomaly Detection in Server Security', '2025-10-05 20:42:37', '2025-10-05 20:42:37');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conference_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `paper_title` varchar(255) NOT NULL,
  `institution` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `presentation_type` varchar(255) NOT NULL,
  `paid_fee` decimal(10,2) DEFAULT NULL,
  `full_paper_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `conference_id` bigint(20) NOT NULL,
  `room_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `conference_id`, `room_name`, `created_at`, `updated_at`) VALUES
(4, 7, 'Athens', '2025-08-23 17:43:32', '2025-08-23 17:43:32'),
(37, 10, 'Beijing', '2025-10-10 23:49:54', '2025-10-10 23:49:54'),
(38, 10, 'Physical (Nanjing)', '2025-10-10 23:49:54', '2025-10-10 23:49:54'),
(39, 10, 'Wuhan', '2025-10-10 23:49:54', '2025-10-10 23:49:54'),
(40, 10, 'Shenzen', '2025-10-10 23:49:54', '2025-10-10 23:49:54'),
(41, 10, 'Guangzhau', '2025-10-10 23:49:54', '2025-10-10 23:49:54'),
(42, 10, 'Hongkong', '2025-10-10 23:49:54', '2025-10-10 23:49:54'),
(43, 10, 'Shanghai', '2025-10-10 23:49:54', '2025-10-10 23:49:54'),
(47, 5, 'Delphi', '2025-10-26 01:00:27', '2025-10-26 01:00:27'),
(48, 5, 'Meteora', '2025-10-26 01:00:27', '2025-10-26 01:00:27'),
(49, 5, 'Santorini', '2025-10-26 01:00:27', '2025-10-26 01:00:27');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('215EsKmXpN51RozCA3MBKYY0lcNWmqNuqi1fVKR4', NULL, '15.235.189.151', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiRHZLVVpqcnhCa2QxMDUzVE84Sk5Kb3NLa3BzenlDM3JBdG40MzFoYSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMjoiaHR0cHM6Ly8xMDMuMTQzLjcxLjIwMiI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIyOiJodHRwczovLzEwMy4xNDMuNzEuMjAyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762098603),
('3h0YJSEBgoGJmsnRgVkcOm26FGBehxob8D66DZhJ', NULL, '139.99.35.44', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUkFMcjJMcVBBczBBd2R5VXAyWWNrSmJHZDdNNm9McURtZjZWYTVvQSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vMTAzLjE0My43MS4yMDIvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762099888),
('fag2AGhsBL19cjyv7Dw8c7jan8fLYr8WgJhermYj', NULL, '141.98.82.26', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYU53cnNhMldDZFR4Qnc5ZmRVSjdJQ2RlS2U1ZnJUVU5xbjlYMzRuVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vMTAzLjE0My43MS4yMDIvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762100805),
('gmK8g0qvAjVn9EOJTQJi158kz8JUeNH7fQ76GB6H', NULL, '141.98.82.26', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaDJxUUphT0FqY1k0bGNxTjZJQ2Rwdm1MczhzYUNoREhLRG05RjF2YiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyMjoiaHR0cHM6Ly8xMDMuMTQzLjcxLjIwMiI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjIyOiJodHRwczovLzEwMy4xNDMuNzEuMjAyIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1762100804),
('hQ8s1mI6HQduBGD9IWip8s4SONSGZM6N9hZiP2U6', NULL, '162.158.122.201', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZVlkb0ozckVDc1lHWk1oQVRwVEU5RE5lcFN3Sk56WEp4bExPMEJiVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTU6Imh0dHBzOi8vYXBwLnNvdHZpLm9yZy9yZWdpc3RyYXRpb24vZGV0YWlsLzY5MDc2OWM5MmE5NTkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762093547),
('lWxliOkRw1RqgKbL6AiiaJgO5DmpAFUkXScQFkDk', 1, '172.69.134.238', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiVzNJeFZRcWVjMjdLWW9VbnFWWHJiRFllNWpEaHVsUHFDRWZ1RURFYiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM1OiJodHRwczovL2FwcC5zb3R2aS5vcmcvaG9tZS9hdWRpZW5jZSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1762092370),
('rt8xRQht0dNcfklh81NpcTirpdbmCbeIPTFKrP2I', NULL, '15.235.189.156', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:134.0) Gecko/20100101 Firefox/134.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWEdtYlF0M1dMeHVrcXJHdU9jQWVQcU02c3RBdnptc3VGczJMTnpJRCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vMTAzLjE0My43MS4yMDIvbG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762099166),
('uaNyflPkY0mn3uHYrtdEhSf1BgMFrC2kogutdo6U', NULL, '162.158.26.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYmZ0ZkZEME5PYmxFcmNUNlc2NXk2TkRFeWNGNGpiZHNTSEpXTnBVbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTU6Imh0dHBzOi8vYXBwLnNvdHZpLm9yZy9yZWdpc3RyYXRpb24vZGV0YWlsLzY5MDc2OWM5MmE5NTkiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1762093813);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'root', 'root@local.system', NULL, '$2y$12$IpHQRkuZKL1ty2g46q4GCO5A1xTM131D8BAcUp0s.PyPER.tF10qW', NULL, '2025-08-12 22:04:16', '2025-08-12 22:04:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audiences`
--
ALTER TABLE `audiences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`),
  ADD KEY `conference_id` (`conference_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `conferences`
--
ALTER TABLE `conferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_id` (`public_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `invoice_history`
--
ALTER TABLE `invoice_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audience_id` (`audience_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `key_notes`
--
ALTER TABLE `key_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audience_id` (`audience_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parallel_sessions`
--
ALTER TABLE `parallel_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `converence_id` (`audience_id`,`room_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registrations_conference_id_foreign` (`conference_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conference_id` (`conference_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `audiences`
--
ALTER TABLE `audiences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `conferences`
--
ALTER TABLE `conferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `key_notes`
--
ALTER TABLE `key_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `parallel_sessions`
--
ALTER TABLE `parallel_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audiences`
--
ALTER TABLE `audiences`
  ADD CONSTRAINT `audiences_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoice_history`
--
ALTER TABLE `invoice_history`
  ADD CONSTRAINT `invoice_history_ibfk_1` FOREIGN KEY (`audience_id`) REFERENCES `audiences` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `key_notes`
--
ALTER TABLE `key_notes`
  ADD CONSTRAINT `key_notes_ibfk_2` FOREIGN KEY (`audience_id`) REFERENCES `audiences` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_conference_id_foreign` FOREIGN KEY (`conference_id`) REFERENCES `conferences` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
