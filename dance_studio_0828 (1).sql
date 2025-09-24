-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025-09-04 08:09:53
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `dance_studio_0828`
--

-- --------------------------------------------------------

--
-- 資料表結構 `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED NOT NULL,
  `campus_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL COMMENT '出勤日期',
  `check_in_time` time DEFAULT NULL COMMENT '簽到時間',
  `check_out_time` time DEFAULT NULL COMMENT '簽退時間',
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'present' COMMENT '出勤狀態',
  `notes` text DEFAULT NULL COMMENT '備註',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `campuses`
--

CREATE TABLE `campuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '校區名稱',
  `address` varchar(255) DEFAULT NULL COMMENT '校區地址',
  `phone` varchar(255) DEFAULT NULL COMMENT '聯絡電話',
  `email` varchar(255) DEFAULT NULL COMMENT '校區電子郵件',
  `description` text DEFAULT NULL COMMENT '校區描述',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否啟用',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '狀態',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序順序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#007bff' COMMENT '校區顏色'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `campuses`
--

INSERT INTO `campuses` (`id`, `name`, `address`, `phone`, `email`, `description`, `is_active`, `status`, `sort_order`, `created_at`, `updated_at`, `color`) VALUES
(1, '小豆芽', '台中市西區精誠路123號', '04-12345678', NULL, '小豆芽舞蹈工作室主要校區', 1, 'active', 1, '2025-09-01 03:15:48', '2025-09-01 03:15:48', '#007bff'),
(2, '永安國小', '台中市西區永安街456號', '04-23456789', NULL, '永安國小舞蹈教室', 1, 'active', 2, '2025-09-01 03:15:48', '2025-09-01 03:15:48', '#28a745'),
(3, '國安國小', '台中市西區國安一路789號', '04-34567890', NULL, '國安國小舞蹈教室', 1, 'active', 3, '2025-09-01 03:15:48', '2025-09-01 03:15:48', '#ffc107'),
(4, '協和國小', '台中市西區協和路321號', '04-45678901', NULL, '協和國小舞蹈教室', 1, 'active', 4, '2025-09-01 03:15:48', '2025-09-04 00:52:21', '#dc3545'),
(5, '協和附幼', '台中市西區協和路321號', '04-45678902', NULL, '協和附幼舞蹈教室', 1, 'active', 5, '2025-09-01 03:15:48', '2025-09-01 03:15:48', '#fd7e14'),
(6, '東海國小', '台中市西區東海路654號', '04-56789012', NULL, '東海國小舞蹈教室', 1, 'active', 6, '2025-09-01 03:15:48', '2025-09-01 03:15:48', '#6f42c1'),
(7, '永寧國小', '台中市西區永寧街987號', '04-67890123', NULL, '永寧國小舞蹈教室', 1, 'active', 7, '2025-09-01 03:15:48', '2025-09-01 03:15:48', '#20c997');

-- --------------------------------------------------------

--
-- 資料表結構 `campus_contacts`
--

CREATE TABLE `campus_contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `campus_id` bigint(20) UNSIGNED NOT NULL,
  `position` varchar(255) NOT NULL COMMENT '職位',
  `name` varchar(255) NOT NULL COMMENT '姓名',
  `phone` varchar(255) NOT NULL COMMENT '聯絡電話',
  `email` varchar(255) DEFAULT NULL COMMENT '電子郵件',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '狀態',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序順序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `campus_contacts`
--

INSERT INTO `campus_contacts` (`id`, `campus_id`, `position`, `name`, `phone`, `email`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, '校區主任', '王小豆芽主任', '04-15561728', 'contact@小豆芽.com', 'active', 1, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(2, 1, '行政助理', '李小豆芽助理', '04-95866588', 'assistant@小豆芽.com', 'active', 2, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(3, 2, '校區主任', '王永安國小主任', '04-63311563', 'contact@永安國小.com', 'active', 1, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(4, 2, '行政助理', '李永安國小助理', '04-37413933', 'assistant@永安國小.com', 'active', 2, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(5, 3, '校區主任', '王國安國小主任', '04-15119133', 'contact@國安國小.com', 'active', 1, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(6, 4, '校區主任', '王協和國小主任', '04-26689466', 'contact@協和國小.com', 'active', 1, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(7, 4, '行政助理', '李協和國小助理', '04-91911950', 'assistant@協和國小.com', 'active', 2, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(8, 5, '校區主任', '王協和附幼主任', '04-73251036', 'contact@協和附幼.com', 'active', 1, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(9, 6, '校區主任', '王東海國小主任', '04-91730004', 'contact@東海國小.com', 'active', 1, '2025-09-01 03:15:49', '2025-09-01 03:15:49'),
(10, 7, '校區主任', '王永寧國小主任', '04-54446268', 'contact@永寧國小.com', 'active', 1, '2025-09-01 03:15:50', '2025-09-01 03:15:50');

-- --------------------------------------------------------

--
-- 資料表結構 `courses`
--

CREATE TABLE `courses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `level` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
  `duration` int(11) NOT NULL DEFAULT 60,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_students` int(11) NOT NULL DEFAULT 20,
  `status` enum('draft','active','inactive','completed','cancelled') NOT NULL DEFAULT 'draft',
  `schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`schedule`)),
  `campus_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否啟用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `courses`
--

INSERT INTO `courses` (`id`, `name`, `description`, `level`, `duration`, `price`, `max_students`, `status`, `schedule`, `campus_id`, `created_at`, `updated_at`, `is_active`) VALUES
(1, '芭蕾舞基礎班', '適合初學者的芭蕾舞基礎課程', 'beginner', 60, 0.00, 20, 'draft', NULL, 1, '2025-09-03 02:17:57', '2025-09-03 02:17:57', 1),
(2, '現代舞進階班', '現代舞技巧提升課程', 'beginner', 60, 0.00, 20, 'draft', NULL, 1, '2025-09-03 02:18:02', '2025-09-03 02:18:02', 1),
(3, '街舞基礎班', '街舞入門課程', 'beginner', 60, 0.00, 20, 'draft', NULL, 4, '2025-09-03 02:18:06', '2025-09-03 02:18:06', 1);

-- --------------------------------------------------------

--
-- 資料表結構 `equipment`
--

CREATE TABLE `equipment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `campus_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('audio','video','lighting','furniture','other') NOT NULL DEFAULT 'other',
  `brand` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(10,2) DEFAULT NULL,
  `status` enum('available','in_use','maintenance','retired') NOT NULL DEFAULT 'available',
  `location` varchar(255) DEFAULT NULL,
  `maintenance_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `failed_jobs`
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
-- 資料表結構 `finances`
--

CREATE TABLE `finances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL COMMENT '財務項目標題',
  `description` text DEFAULT NULL COMMENT '財務項目描述',
  `type` enum('income','expense') NOT NULL COMMENT '收入/支出',
  `amount` decimal(12,2) NOT NULL COMMENT '金額',
  `campus_id` bigint(20) UNSIGNED NOT NULL,
  `course_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transaction_date` date NOT NULL COMMENT '交易日期',
  `payment_method` varchar(255) DEFAULT NULL COMMENT '付款方式',
  `reference_number` varchar(255) DEFAULT NULL COMMENT '參考號碼',
  `notes` text DEFAULT NULL COMMENT '備註',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `jobs`
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
-- 資料表結構 `job_batches`
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
-- 資料表結構 `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_28_144527_create_campuses_table', 1),
(5, '2025_08_28_151014_create_campus_contacts_table', 1),
(6, '2025_08_28_152055_add_color_to_campuses_table', 1),
(7, '2025_08_28_153905_create_user_roles_table', 1),
(8, '2025_08_28_154126_create_permissions_table', 1),
(9, '2025_08_28_154143_create_role_permissions_table', 1),
(10, '2025_08_28_154330_modify_users_table_add_role_and_campus_fields', 1),
(11, '2025_08_29_114946_create_school_events_table', 1),
(12, '2025_08_29_130916_update_school_events_category_enum', 1),
(13, '2025_09_01_103950_create_courses_table', 1),
(14, '2025_09_01_103951_create_attendances_table', 1),
(15, '2025_09_01_103952_create_equipment_table', 1),
(16, '2025_09_01_110742_create_finances_table', 1),
(17, '2025_09_01_074159_create_campuses_table', 1),
(18, '2025_09_01_080000_create_users_table', 1),
(19, '2025_09_01_074204_create_courses_table', 1),
(20, '2025_09_01_074221_create_equipment_table', 1),
(25, '2025_09_01_074200_create_school_events_table', 2),
(26, '2025_09_01_080143_create_roles_table', 2),
(27, '2025_09_01_080910_add_role_foreign_key_to_users_table', 2),
(28, '2025_09_01_081000_create_finances_table', 2),
(29, '2025_09_01_081100_create_attendances_table', 2),
(30, '2025_09_01_090000_create_system_settings_table', 3),
(31, '2025_09_01_090100_create_audit_logs_table', 3),
(32, '2025_09_02_120058_create_finances_table_fix', 4),
(33, '2025_09_02_120129_create_attendances_table_fix', 5),
(34, '2025_09_02_135014_create_notifications_table', 6),
(36, '2025_09_02_141415_add_email_to_campuses_table', 7),
(37, '2025_09_02_142703_add_is_active_to_campuses_table', 8),
(39, '2025_09_02_144806_add_campus_id_to_equipment_table', 9),
(45, '2025_09_03_000001_add_missing_columns_to_school_events', 10),
(46, '2025_09_03_000002_fix_status_column_length', 10),
(47, '2025_09_03_000003_fix_existing_school_events_data', 10),
(49, '2025_09_03_000004_create_sample_school_events_for_campus_4', 11),
(50, '2025_09_03_101647_fix_courses_table_structure', 11),
(51, '2025_09_03_000004_fix_school_events_column_names', 12),
(52, '2025_09_03_000000_consolidate_school_events_table', 13);

-- --------------------------------------------------------

--
-- 資料表結構 `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '權限名稱',
  `display_name` varchar(255) NOT NULL COMMENT '顯示名稱',
  `description` text DEFAULT NULL COMMENT '權限描述',
  `module` varchar(255) NOT NULL COMMENT '所屬模組',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '狀態',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序順序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `display_name`, `description`, `module`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'campus.view', '查看校區', '查看校區列表和詳細資料', 'campus', 'active', 1, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(2, 'campus.create', '建立校區', '建立新的校區', 'campus', 'active', 2, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(3, 'campus.edit', '編輯校區', '編輯校區資料', 'campus', 'active', 3, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(4, 'campus.delete', '刪除校區', '刪除校區', 'campus', 'active', 4, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(5, 'user.view', '查看用戶', '查看用戶列表和詳細資料', 'user', 'active', 5, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(6, 'user.create', '建立用戶', '建立新的用戶', 'user', 'active', 6, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(7, 'user.edit', '編輯用戶', '編輯用戶資料', 'user', 'active', 7, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(8, 'user.delete', '刪除用戶', '刪除用戶', 'user', 'active', 8, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(9, 'user.role', '管理用戶角色', '管理用戶角色和權限', 'user', 'active', 9, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(10, 'course.view', '查看課程', '查看課程列表和詳細資料', 'course', 'active', 10, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(11, 'course.create', '建立課程', '建立新的課程', 'course', 'active', 11, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(12, 'course.edit', '編輯課程', '編輯課程資料', 'course', 'active', 12, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(13, 'course.delete', '刪除課程', '刪除課程', 'course', 'active', 13, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(14, 'attendance.view', '查看出勤', '查看出勤記錄', 'attendance', 'active', 14, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(15, 'attendance.create', '建立出勤', '建立出勤記錄', 'attendance', 'active', 15, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(16, 'attendance.edit', '編輯出勤', '編輯出勤記錄', 'attendance', 'active', 16, '2025-09-01 03:15:47', '2025-09-01 03:15:47'),
(17, 'equipment.view', '查看設備', '查看設備列表和詳細資料', 'equipment', 'active', 17, '2025-09-01 03:15:48', '2025-09-01 03:15:48'),
(18, 'equipment.create', '建立設備', '建立新的設備', 'equipment', 'active', 18, '2025-09-01 03:15:48', '2025-09-01 03:15:48'),
(19, 'equipment.edit', '編輯設備', '編輯設備資料', 'equipment', 'active', 19, '2025-09-01 03:15:48', '2025-09-01 03:15:48'),
(20, 'equipment.delete', '刪除設備', '刪除設備', 'equipment', 'active', 20, '2025-09-01 03:15:48', '2025-09-01 03:15:48'),
(21, 'finance.view', '查看財務', '查看財務記錄', 'finance', 'active', 21, '2025-09-01 03:15:48', '2025-09-01 03:15:48'),
(22, 'finance.create', '建立財務', '建立財務記錄', 'finance', 'active', 22, '2025-09-01 03:15:48', '2025-09-01 03:15:48'),
(23, 'finance.edit', '編輯財務', '編輯財務記錄', 'finance', 'active', 23, '2025-09-01 03:15:48', '2025-09-01 03:15:48'),
(24, 'system.view', '查看系統', '查看系統設定', 'system', 'active', 24, '2025-09-01 03:15:48', '2025-09-01 03:15:48'),
(25, 'system.edit', '編輯系統', '編輯系統設定', 'system', 'active', 25, '2025-09-01 03:15:48', '2025-09-01 03:15:48');

-- --------------------------------------------------------

--
-- 資料表結構 `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `permissions` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否啟用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '管理員', NULL, '系統管理員，擁有所有權限', 1, '2025-09-02 03:45:06', '2025-09-02 03:45:06'),
(2, '教師', NULL, '舞蹈教師，可以管理課程和學生', 1, '2025-09-02 03:45:06', '2025-09-02 03:45:06'),
(3, '學生', NULL, '舞蹈學生，可以查看課程和報名', 1, '2025-09-02 03:45:06', '2025-09-02 03:45:06'),
(4, '前台', NULL, '前台工作人員，可以處理報名和諮詢', 1, '2025-09-02 03:45:06', '2025-09-02 03:45:06');

-- --------------------------------------------------------

--
-- 資料表結構 `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `school_events`
--

CREATE TABLE `school_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `is_all_day` tinyint(1) NOT NULL DEFAULT 0,
  `color` varchar(255) NOT NULL DEFAULT '#007bff',
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `has_reminder` tinyint(1) NOT NULL DEFAULT 0,
  `reminder_minutes` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `google_calendar_id` varchar(255) DEFAULT NULL,
  `sync_to_google` tinyint(1) NOT NULL DEFAULT 0,
  `last_synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL COMMENT '事件類型',
  `campus_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `school_events`
--

INSERT INTO `school_events` (`id`, `title`, `description`, `location`, `start_time`, `end_time`, `is_all_day`, `color`, `tags`, `has_reminder`, `reminder_minutes`, `status`, `priority`, `created_by`, `assigned_to`, `google_calendar_id`, `sync_to_google`, `last_synced_at`, `created_at`, `updated_at`, `category`, `campus_id`) VALUES
(1, '期中考試', '全校期中考試，請各科老師準備試題', '各教室', '2025-09-16 08:00:00', '2025-09-16 17:00:00', 1, '#007bff', '[\"\\u8003\\u8a66\",\"\\u671f\\u4e2d\\u8003\"]', 0, NULL, 'pending', 'high', 5, 2, NULL, 0, NULL, '2025-09-01 03:15:51', '2025-09-01 03:15:51', 'other', 1),
(2, '期末考試', '學期期末考試', '各教室', '2025-10-16 08:00:00', '2025-10-16 17:00:00', 1, '#007bff', '[\"\\u8003\\u8a66\",\"\\u671f\\u672b\\u8003\"]', 0, NULL, 'pending', 'high', 2, 1, NULL, 0, NULL, '2025-09-01 03:15:51', '2025-09-01 03:15:51', 'other', 1),
(3, '校園運動會', '年度校園運動會，包含田徑、球類等比賽項目', '操場', '2025-10-01 08:00:00', '2025-10-01 18:00:00', 1, '#007bff', '[\"\\u904b\\u52d5\\u6703\",\"\\u6bd4\\u8cfd\"]', 0, NULL, 'pending', 'medium', 2, 3, NULL, 0, NULL, '2025-09-01 03:15:51', '2025-09-01 03:15:51', 'other', 1),
(4, '籃球比賽', '班際籃球比賽', '籃球場', '2025-09-11 14:00:00', '2025-09-11 16:00:00', 0, '#007bff', '[\"\\u7c43\\u7403\",\"\\u6bd4\\u8cfd\"]', 0, NULL, 'pending', 'medium', 5, 3, NULL, 0, NULL, '2025-09-01 03:15:51', '2025-09-01 03:15:51', 'other', 1),
(5, '財務報表', '準備本月財務報表', '辦公室', '2025-09-06 09:00:00', '2025-09-06 12:00:00', 0, '#007bff', '[\"\\u8ca1\\u52d9\",\"\\u5831\\u8868\"]', 0, NULL, 'pending', 'medium', 4, 5, NULL, 0, NULL, '2025-09-01 03:15:52', '2025-09-01 03:15:52', 'other', 1),
(6, '美術展覽', '學生美術作品展覽', '美術教室', '2025-09-26 09:00:00', '2025-09-28 17:00:00', 1, '#007bff', '[\"\\u5c55\\u89bd\",\"\\u7f8e\\u8853\"]', 0, NULL, 'pending', 'low', 2, 5, NULL, 0, NULL, '2025-09-01 03:15:52', '2025-09-01 03:15:52', 'other', 1),
(7, '教師會議', '每月教師會議，討論教學進度和學生狀況', '會議室', '2025-09-06 14:00:00', '2025-09-06 16:00:00', 0, '#007bff', '[\"\\u6703\\u8b70\",\"\\u6559\\u5e2b\"]', 0, NULL, 'pending', 'medium', 2, 3, NULL, 0, NULL, '2025-09-01 03:15:52', '2025-09-01 03:15:52', 'other', 1),
(8, '家長會', '學期家長會，與家長溝通學生學習狀況', '禮堂', '2025-10-06 19:00:00', '2025-10-06 21:00:00', 0, '#007bff', '[\"\\u5bb6\\u9577\\u6703\",\"\\u6e9d\\u901a\"]', 0, NULL, 'pending', 'high', 1, 3, NULL, 0, NULL, '2025-09-01 03:15:52', '2025-09-01 03:15:52', 'other', 1),
(9, '設備維護', '教室設備定期維護檢查', '各教室', '2025-09-09 09:00:00', '2025-09-09 17:00:00', 1, '#007bff', '[\"\\u7dad\\u8b77\",\"\\u8a2d\\u5099\"]', 0, NULL, 'pending', 'medium', 3, 1, NULL, 0, NULL, '2025-09-01 03:15:52', '2025-09-01 03:15:52', 'other', 1),
(10, '財務報表', '準備本月財務報表', '辦公室', '2025-09-04 09:00:00', '2025-09-04 12:00:00', 0, '#007bff', '[\"\\u8ca1\\u52d9\",\"\\u5831\\u8868\"]', 0, NULL, 'pending', 'high', 3, 2, NULL, 0, NULL, '2025-09-01 03:15:52', '2025-09-01 03:15:52', 'other', 1),
(11, '校園清潔日', '全校師生共同參與的校園清潔活動', '校園各處', '2025-09-13 08:00:00', '2025-09-13 10:00:00', 0, '#007bff', '[\"\\u6e05\\u6f54\",\"\\u74b0\\u4fdd\"]', 0, NULL, 'pending', 'low', 3, 2, NULL, 0, NULL, '2025-09-01 03:15:52', '2025-09-01 03:15:52', 'other', 1),
(19, '校區 4 舞蹈課程開課', '為校區 4 的學員開設基礎舞蹈課程', '校區 4 舞蹈教室 A', '2025-09-10 10:17:34', '2025-09-10 12:17:34', 0, '#007bff', NULL, 0, NULL, 'active', 'medium', 1, NULL, NULL, 0, NULL, '2025-09-03 02:17:34', '2025-09-03 06:44:42', 'meeting', 4),
(20, '校區 4 表演活動', '校區 4 學員成果展示表演', '校區 4 表演廳', '2025-09-17 10:17:34', '2025-09-17 13:17:34', 0, '#007bff', NULL, 0, NULL, 'active', 'medium', 1, NULL, NULL, 0, NULL, '2025-09-03 02:17:34', '2025-09-03 02:17:34', 'performance', 4),
(21, '校區 4 教師會議', '討論校區 4 的教學計劃和學員進度', '校區 4 會議室', '2025-09-05 10:17:34', '2025-09-05 11:17:34', 0, '#007bff', NULL, 0, NULL, 'active', 'medium', 1, NULL, NULL, 0, NULL, '2025-09-03 02:17:34', '2025-09-03 02:17:34', 'meeting', 4),
(25, '11', NULL, NULL, '2025-09-18 00:00:00', '2025-09-24 00:00:00', 0, '#007bff', NULL, 0, NULL, 'active', 'medium', 1, NULL, NULL, 0, NULL, '2025-09-04 04:36:46', '2025-09-04 04:36:46', 'course', 4);

-- --------------------------------------------------------

--
-- 資料表結構 `sessions`
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
-- 傾印資料表的資料 `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('MFJkNsikyPTLfSBCyFdCTICixY5yPQbuj7uDyR6c', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoibTJkWXU4TXV5V0lUVG5aRFRxT05jTG05ZFYxV3hIZmVSN1JRSHh2OCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vY2FtcHVzZXMvND9yZWxhdGlvbj00Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2MDoiJDJ5JDEyJHo4b1VxRzdyOVQwLzNaNDBDek9QYnVYMklnbzdTVG1PNmNxYWhvMmtIZ0hzLjU0dHF4b2lDIjtzOjY6InRhYmxlcyI7YToxMDp7czo0MDoiMWU3ZjYzNTk3NzkxOWE1MTdmODIwZTc4Y2E5ZTkyNjZfY29sdW1ucyI7YToxMDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6InRpdGxlIjtzOjU6ImxhYmVsIjtzOjEyOiLkuovku7bmqJnpoYwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJkZXNjcmlwdGlvbiI7czo1OiJsYWJlbCI7czoxMjoi5LqL5Lu25o+P6L+wIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic3RhcnRfdGltZSI7czo1OiJsYWJlbCI7czoxMjoi6ZaL5aeL5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJlbmRfdGltZSI7czo1OiJsYWJlbCI7czoxMjoi57WQ5p2f5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJsb2NhdGlvbiI7czo1OiJsYWJlbCI7czo2OiLlnLDpu54iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImNhdGVnb3J5IjtzOjU6ImxhYmVsIjtzOjEyOiLkuovku7bpoZ7lnosiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo2OiLni4DmhYsiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo3O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJjcmVhdG9yLm5hbWUiO3M6NToibGFiZWwiO3M6OToi5bu656uL6ICFIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMjoi5bu656uL5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo5O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjEyOiLmm7TmlrDmmYLplpMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiYjcyNGI3Mjc4NGYzYTVmODViMmU2YzZjY2VkMjUxOTZfY29sdW1ucyI7YTo3OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czoxMjoi5qCh5Y2A5ZCN56ixIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJhZGRyZXNzIjtzOjU6ImxhYmVsIjtzOjY6IuWcsOWdgCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToicGhvbmUiO3M6NToibGFiZWwiO3M6Njoi6Zu76KmxIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJlbWFpbCI7czo1OiJsYWJlbCI7czoxODoi6Zu75a2Q6YO15Lu25Zyw5Z2AIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6MTI6IuWVn+eUqOeLgOaFiyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuW7uueri+aZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoidXBkYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMjoi5pu05paw5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjZhYzkwZGZjNzVhNTllM2NiZjhiNjdjNDc5M2VjMjkzX2NvbHVtbnMiO2E6MTA6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJ0aXRsZSI7czo1OiJsYWJlbCI7czo2OiLmqJnpoYwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzdGFydF90aW1lIjtzOjU6ImxhYmVsIjtzOjEyOiLplovlp4vmmYLplpMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImNhdGVnb3J5IjtzOjU6ImxhYmVsIjtzOjY6IumhnuWeiyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IueLgOaFiyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImRlc2NyaXB0aW9uIjtzOjU6ImxhYmVsIjtzOjY6IuaPj+i/sCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJlbmRfdGltZSI7czo1OiJsYWJlbCI7czoxMjoi57WQ5p2f5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImxvY2F0aW9uIjtzOjU6ImxhYmVsIjtzOjY6IuWcsOm7niI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoiY3JlYXRvci5uYW1lIjtzOjU6ImxhYmVsIjtzOjk6IuW7uueri+iAhSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMjoi5bu656uL5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo5O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjEyOiLmm7TmlrDmmYLplpMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MDoiMjgxYzlmMjJjMDYyZmJkZTIxYTM2YTZjODNlNTE4MDhfY29sdW1ucyI7YTo5OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czoxMjoi6Kqy56iL5ZCN56ixIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJwcmljZSI7czo1OiJsYWJlbCI7czoxMjoi6Kqy56iL5YO55qC8IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJkdXJhdGlvbiI7czo1OiJsYWJlbCI7czoxMjoi6Kqy56iL5pmC6ZW3IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoibWF4X3N0dWRlbnRzIjtzOjU6ImxhYmVsIjtzOjE1OiLmnIDlpKflrbjlk6HmlbgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJjYW1wdXMubmFtZSI7czo1OiJsYWJlbCI7czoxMjoi5qCh5Y2A5ZCN56ixIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJsZXZlbCI7czo1OiJsYWJlbCI7czoxMjoi6Kqy56iL562J57SaIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6MTI6IuWVn+eUqOeLgOaFiyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuW7uueri+aZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoidXBkYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMjoi5pu05paw5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjZkZGY1NThiNGMzZmM4NmVlMDBkOTE1NjBhMjJjZGQzX2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6MTI6IueUqOaItuWnk+WQjSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToiZW1haWwiO3M6NToibGFiZWwiO3M6MTI6Iumbu+WtkOmDteS7tiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicm9sZS5uYW1lIjtzOjU6ImxhYmVsIjtzOjY6IuinkuiJsiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTc6ImVtYWlsX3ZlcmlmaWVkX2F0IjtzOjU6ImxhYmVsIjtzOjI0OiLpm7vlrZDpg7Xku7bpqZforYnmmYLplpMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuW7uueri+aZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoidXBkYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMjoi5pu05paw5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjEzODY2ODVjNDkwODQ2ZDBmYWQzY2I2MzZhZDY3MzEzX2NvbHVtbnMiO2E6ODp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6MTI6IuioreWCmeWQjeeosSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6InNlcmlhbF9udW1iZXIiO3M6NToibGFiZWwiO3M6Njoi5bqP6JmfIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6Njoi54uA5oWLIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiY2FtcHVzLm5hbWUiO3M6NToibGFiZWwiO3M6MTI6IuagoeWNgOWQjeeosSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6InB1cmNoYXNlX2RhdGUiO3M6NToibGFiZWwiO3M6MTI6IuizvOiyt+aXpeacnyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InB1cmNoYXNlX3ByaWNlIjtzOjU6ImxhYmVsIjtzOjEyOiLos7zosrflg7nmoLwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjEyOiLlu7rnq4vmmYLplpMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuabtOaWsOaZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJjNzNkYTllOTZmMTFhZjYwMjhjODBhMWI2NmQ0OWRkZV9jb2x1bW5zIjthOjExOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToidGl0bGUiO3M6NToibGFiZWwiO3M6MTg6IuiyoeWLmemgheebruaomemhjCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoidHlwZSI7czo1OiJsYWJlbCI7czoxMjoi6LKh5YuZ6aGe5Z6LIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJhbW91bnQiO3M6NToibGFiZWwiO3M6Njoi6YeR6aGNIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiY2FtcHVzLm5hbWUiO3M6NToibGFiZWwiO3M6MTI6IuagoeWNgOWQjeeosSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImNvdXJzZS5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiLoqrLnqIvlkI3nqLEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6InVzZXIubmFtZSI7czo1OiJsYWJlbCI7czoxMjoi55So5oi25aeT5ZCNIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoidHJhbnNhY3Rpb25fZGF0ZSI7czo1OiJsYWJlbCI7czoxMjoi5Lqk5piT5pel5pyfIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoicGF5bWVudF9tZXRob2QiO3M6NToibGFiZWwiO3M6MTI6IuS7mOasvuaWueW8jyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6InJlZmVyZW5jZV9udW1iZXIiO3M6NToibGFiZWwiO3M6MTI6IuWPg+iAg+iZn+eivCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjk7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuW7uueri+aZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MTA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuabtOaWsOaZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiJjYTk5N2EyMmUxMDUxNzJmZmI1ZGM4MGQ4MDllYjM1Yl9jb2x1bW5zIjthOjk6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJ1c2VyLm5hbWUiO3M6NToibGFiZWwiO3M6MTI6IuWtuOWToeWnk+WQjSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImNvdXJzZS5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiLoqrLnqIvlkI3nqLEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6ImRhdGUiO3M6NToibGFiZWwiO3M6MTI6IuS4iuiqsuaXpeacnyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImNoZWNrX2luX3RpbWUiO3M6NToibGFiZWwiO3M6MTI6IuewveWIsOaZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6ImNoZWNrX291dF90aW1lIjtzOjU6ImxhYmVsIjtzOjEyOiLnsL3pgIDmmYLplpMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czoxMjoi5Ye65Yuk54uA5oWLIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJub3RlcyI7czo1OiJsYWJlbCI7czo2OiLlgpnoqLsiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuW7uueri+aZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoidXBkYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMjoi5pu05paw5pmC6ZaTIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjZiZTRlYmI3MmViMjcyYzJlOTVhY2IxOWUxZmFmYmFmX2NvbHVtbnMiO2E6ODp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6MTI6IuioreWCmeWQjeeosSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6InNlcmlhbF9udW1iZXIiO3M6NToibGFiZWwiO3M6Njoi5bqP6JmfIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6Njoi54uA5oWLIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiY2FtcHVzLm5hbWUiO3M6NToibGFiZWwiO3M6MTI6IuagoeWNgOWQjeeosSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6InB1cmNoYXNlX2RhdGUiO3M6NToibGFiZWwiO3M6MTI6IuizvOiyt+aXpeacnyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InB1cmNoYXNlX3ByaWNlIjtzOjU6ImxhYmVsIjtzOjEyOiLos7zosrflg7nmoLwiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjEyOiLlu7rnq4vmmYLplpMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuabtOaWsOaZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiIxMWNiYjVhMWRiMWFkZDRiMWUzZmI3MjkzNTAwZTc2MF9jb2x1bW5zIjthOjExOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToidGl0bGUiO3M6NToibGFiZWwiO3M6MTg6IuiyoeWLmemgheebruaomemhjCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoidHlwZSI7czo1OiJsYWJlbCI7czoxMjoi6LKh5YuZ6aGe5Z6LIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJhbW91bnQiO3M6NToibGFiZWwiO3M6Njoi6YeR6aGNIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiY2FtcHVzLm5hbWUiO3M6NToibGFiZWwiO3M6MTI6IuagoeWNgOWQjeeosSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImNvdXJzZS5uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiLoqrLnqIvlkI3nqLEiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6InVzZXIubmFtZSI7czo1OiJsYWJlbCI7czoxMjoi55So5oi25aeT5ZCNIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNjoidHJhbnNhY3Rpb25fZGF0ZSI7czo1OiJsYWJlbCI7czoxMjoi5Lqk5piT5pel5pyfIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoicGF5bWVudF9tZXRob2QiO3M6NToibGFiZWwiO3M6MTI6IuS7mOasvuaWueW8jyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTY6InJlZmVyZW5jZV9udW1iZXIiO3M6NToibGFiZWwiO3M6MTI6IuWPg+iAg+iZn+eivCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjk7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuW7uueri+aZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6MTA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IuabtOaWsOaZgumWkyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX19czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1756966002);

-- --------------------------------------------------------

--
-- 資料表結構 `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `description` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `system_settings`
--

INSERT INTO `system_settings` (`id`, `key`, `value`, `description`, `type`, `created_at`, `updated_at`) VALUES
(1, 'site_name', '\"\\u821e\\u8e48\\u5de5\\u4f5c\\u5ba4\\u7ba1\\u7406\\u7cfb\\u7d71\"', '網站名稱', 'string', '2025-09-02 03:58:49', '2025-09-02 03:58:49'),
(2, 'site_description', '\"\\u5c08\\u696d\\u7684\\u821e\\u8e48\\u5de5\\u4f5c\\u5ba4\\u7ba1\\u7406\\u5e73\\u53f0\"', '網站描述', 'string', '2025-09-02 03:58:49', '2025-09-02 03:58:49'),
(3, 'contact_email', '\"admin@dance.com\"', '聯絡電子郵件', 'string', '2025-09-02 03:58:49', '2025-09-02 03:58:49'),
(4, 'contact_phone', '\"+886-2-1234-5678\"', '聯絡電話', 'string', '2025-09-02 03:58:49', '2025-09-02 03:58:49'),
(5, 'max_students_per_course', '\"20\"', '每門課程最大學生數', 'number', '2025-09-02 03:58:49', '2025-09-02 03:58:49'),
(6, 'enable_registration', '\"true\"', '是否啟用學生註冊', 'boolean', '2025-09-02 03:58:49', '2025-09-02 03:58:49');

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `campus_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT '最後登入時間',
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL COMMENT '職務',
  `phone` varchar(255) DEFAULT NULL,
  `parent_name` varchar(255) DEFAULT NULL COMMENT '家長姓名',
  `parent_phone` varchar(255) DEFAULT NULL COMMENT '家長聯絡電話',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `users`
--

INSERT INTO `users` (`id`, `campus_id`, `name`, `email`, `email_verified_at`, `last_login_at`, `password`, `role_id`, `position`, `phone`, `parent_name`, `parent_phone`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, '超級管理者', 'super@dance.com', NULL, NULL, '$2y$12$z8oUqG7r9T0/3Z40CzOPbuX2Igo7STmO6cqaho2kHgHs.54tqxoiC', 1, NULL, '0912345678', NULL, NULL, 'active', 'GgTbwQe24aWzVGu55AzWG9k2zpxl0CjyKToqD6M98Aw0Y6bLJU9KPdcOtt7N', '2025-09-01 03:15:50', '2025-09-01 03:15:50'),
(2, NULL, '系統管理者', 'admin@dance.com', NULL, NULL, '$2y$12$JOW/7h20XL6c2tVqqAMV4Orr2yXxG1qcuyKj0zq2ETm5BdnHiRAjq', 1, NULL, '0923456789', NULL, NULL, 'active', 'AVVby3ESXAsXuDsvGUGYoIajTPNPNSXSvn6H3uf2qbPLCeuVrpL3BpDF90KA', '2025-09-01 03:15:50', '2025-09-01 03:15:50'),
(3, NULL, '校務窗口', 'academic@dance.com', NULL, NULL, '$2y$12$tmseB5Jq6WRC0uV6AZrPTOjRa5iCmNqdYLmhF37OQIrG9ym6S7jj6', 1, NULL, '0934567890', NULL, NULL, 'active', NULL, '2025-09-01 03:15:50', '2025-09-01 03:15:50'),
(4, NULL, '舞蹈老師', 'teacher@dance.com', NULL, NULL, '$2y$12$eX7I5c.Syb21I1grVV4kPOfcCCLDDUCK7EVjvfmDGtGwka8afPyaS', 1, NULL, '0945678901', NULL, NULL, 'active', NULL, '2025-09-01 03:15:50', '2025-09-01 03:15:50'),
(5, NULL, '舞蹈學生', 'student@dance.com', NULL, NULL, '$2y$12$Fkz35mD7OUtcTas79rbUVe9kzT1SChzN0LAl4DDkT0vdIKv9xw42m', 1, NULL, '0956789012', NULL, NULL, 'active', NULL, '2025-09-01 03:15:51', '2025-09-01 03:15:51');

-- --------------------------------------------------------

--
-- 資料表結構 `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '角色名稱',
  `display_name` varchar(255) NOT NULL COMMENT '顯示名稱',
  `description` text DEFAULT NULL COMMENT '角色描述',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active' COMMENT '狀態',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序順序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- 傾印資料表的資料 `user_roles`
--

INSERT INTO `user_roles` (`id`, `name`, `display_name`, `description`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'super_admin', '超級管理者', '擁有系統所有權限，可以管理所有功能模組', 'active', 1, '2025-09-01 03:15:46', '2025-09-01 03:15:46'),
(2, 'admin', '管理者', '擁有大部分管理權限，由超級管理者授權', 'active', 2, '2025-09-01 03:15:46', '2025-09-01 03:15:46'),
(3, 'academic', '校務窗口', '負責校區行政事務，管理校區相關業務', 'active', 3, '2025-09-01 03:15:46', '2025-09-01 03:15:46'),
(4, 'teacher', '老師', '負責教學工作，管理課程和學生', 'active', 4, '2025-09-01 03:15:46', '2025-09-01 03:15:46'),
(5, 'student', '學生', '系統使用者，可以查看自己的課程和出勤記錄', 'active', 5, '2025-09-01 03:15:47', '2025-09-01 03:15:47');

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_user_id_foreign` (`user_id`),
  ADD KEY `attendances_course_id_foreign` (`course_id`),
  ADD KEY `attendances_campus_id_foreign` (`campus_id`);

--
-- 資料表索引 `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `audit_logs_user_id_index` (`user_id`),
  ADD KEY `audit_logs_action_index` (`action`);

--
-- 資料表索引 `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- 資料表索引 `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- 資料表索引 `campuses`
--
ALTER TABLE `campuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campuses_name_index` (`name`),
  ADD KEY `campuses_status_index` (`status`),
  ADD KEY `campuses_sort_order_index` (`sort_order`);

--
-- 資料表索引 `campus_contacts`
--
ALTER TABLE `campus_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campus_contacts_campus_id_index` (`campus_id`),
  ADD KEY `campus_contacts_status_index` (`status`),
  ADD KEY `campus_contacts_sort_order_index` (`sort_order`);

--
-- 資料表索引 `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `courses_category_level_index` (`level`),
  ADD KEY `courses_status_start_date_index` (`status`),
  ADD KEY `courses_campus_id_index` (`campus_id`);

--
-- 資料表索引 `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_category_status_index` (`category`,`status`),
  ADD KEY `equipment_location_index` (`location`),
  ADD KEY `equipment_purchase_date_index` (`purchase_date`),
  ADD KEY `equipment_campus_id_foreign` (`campus_id`);

--
-- 資料表索引 `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- 資料表索引 `finances`
--
ALTER TABLE `finances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `finances_campus_id_foreign` (`campus_id`),
  ADD KEY `finances_course_id_foreign` (`course_id`),
  ADD KEY `finances_user_id_foreign` (`user_id`);

--
-- 資料表索引 `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- 資料表索引 `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- 資料表索引 `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- 資料表索引 `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`),
  ADD KEY `permissions_name_index` (`name`),
  ADD KEY `permissions_module_index` (`module`),
  ADD KEY `permissions_status_index` (`status`),
  ADD KEY `permissions_sort_order_index` (`sort_order`);

--
-- 資料表索引 `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- 資料表索引 `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  ADD KEY `role_permissions_role_id_index` (`role_id`),
  ADD KEY `role_permissions_permission_id_index` (`permission_id`);

--
-- 資料表索引 `school_events`
--
ALTER TABLE `school_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_events_created_by_foreign` (`created_by`),
  ADD KEY `school_events_assigned_to_foreign` (`assigned_to`),
  ADD KEY `school_events_start_time_end_time_index` (`start_time`,`end_time`),
  ADD KEY `school_events_category_status_index` (`status`),
  ADD KEY `school_events_campus_id_start_time_index` (`start_time`),
  ADD KEY `school_events_google_calendar_id_index` (`google_calendar_id`),
  ADD KEY `school_events_campus_id_foreign` (`campus_id`);

--
-- 資料表索引 `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- 資料表索引 `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_unique` (`key`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_campus_id_index` (`campus_id`),
  ADD KEY `users_position_index` (`position`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- 資料表索引 `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_name_unique` (`name`),
  ADD KEY `user_roles_name_index` (`name`),
  ADD KEY `user_roles_status_index` (`status`),
  ADD KEY `user_roles_sort_order_index` (`sort_order`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `campuses`
--
ALTER TABLE `campuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `campus_contacts`
--
ALTER TABLE `campus_contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `courses`
--
ALTER TABLE `courses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `finances`
--
ALTER TABLE `finances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `school_events`
--
ALTER TABLE `school_events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- 資料表的限制式 `campus_contacts`
--
ALTER TABLE `campus_contacts`
  ADD CONSTRAINT `campus_contacts_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `finances`
--
ALTER TABLE `finances`
  ADD CONSTRAINT `finances_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `finances_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `finances_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- 資料表的限制式 `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `school_events`
--
ALTER TABLE `school_events`
  ADD CONSTRAINT `school_events_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `school_events_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `school_events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- 資料表的限制式 `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`),
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
