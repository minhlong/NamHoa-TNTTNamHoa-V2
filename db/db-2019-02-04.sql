
DROP TABLE IF EXISTS `thiet_bi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thiet_bi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ten` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `trang_thai` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tai_khoan_id` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ngay_muon` date DEFAULT NULL,
  `ngay_tra` date DEFAULT NULL,
  `ghi_chu` text COLLATE utf8_unicode_ci,
  `tai_khoan_cap_nhat` char(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `thiet_bi_tai_khoan_id_foreign` (`tai_khoan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;


LOCK TABLES `phan_quyen` WRITE;
DELETE FROM `phan_quyen` WHERE id = 9;
INSERT INTO `phan_quyen` VALUES (9,'thiet-bi','Thiết Bị','- Quản lý thông tin thiết bị','2015-07-19 00:08:30','2017-11-20 15:24:12');
UNLOCK TABLES;