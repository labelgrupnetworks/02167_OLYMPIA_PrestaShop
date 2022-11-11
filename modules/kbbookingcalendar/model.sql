CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_category` (
    `id_booking_category` int(10) unsigned NOT NULL auto_increment,
    `description` text,
    `active` TINYINT(1) NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_booking_category`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_category_lang` (
    `id_booking_category_lang` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_booking_category` int(11) UNSIGNED NOT NULL,
    `id_lang` int(11) UNSIGNED NOT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id_booking_category_lang`),
    FOREIGN KEY (id_booking_category) references _PREFIX_kb_booking_category(id_booking_category) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_category_shop` (
    `id_booking_category_shop` int(10) unsigned NOT NULL auto_increment,
    `id_booking_category` int(10) unsigned DEFAULT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id_booking_category_shop`),
     FOREIGN KEY (id_booking_category) references _PREFIX_kb_booking_category(id_booking_category) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_room_type` (
    `id_room_type` int(10) unsigned NOT NULL auto_increment,
    `max_allowed_child` int(10) unsigned DEFAULT NULL,
    `max_allowed_adult` int(10) unsigned DEFAULT NULL,
    `room_category` text DEFAULT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
  PRIMARY KEY (`id_room_type`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_room_type_lang` (
    `id_room_type_lang` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_room_type` int(11) UNSIGNED NOT NULL,
    `id_lang` int(11) UNSIGNED NOT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    `room_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_room_type_lang`),
 FOREIGN KEY (id_room_type) references _PREFIX_kb_booking_room_type(id_room_type) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_room_type_shop` (
    `id_room_type_shop` int(10) unsigned NOT NULL auto_increment,
    `id_room_type` int(10) unsigned DEFAULT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id_room_type_shop`),
     FOREIGN KEY (id_room_type) references _PREFIX_kb_booking_room_type(id_room_type) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_price_rule` (
    `id_booking_price_rule` int(10) unsigned NOT NULL auto_increment,
    `id_product` int(10) unsigned NOT NULL DEFAULT '0',
    `date_selection` enum('date_range','particular_date') NOT NULL DEFAULT 'date_range',
    `start_date` date NOT NULL,
    `end_date` date NOT NULL,
    `particular_date` date NOT NULL,
    `reduction_type` enum('fixed','percentage') NOT NULL DEFAULT 'percentage',
--     `reduction_tax` enum('0','1') NOT NULL DEFAULT '0',
     `reduction`  decimal(15,2)  DEFAULT NULL,
    `active` TINYINT(1) NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_booking_price_rule`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_price_rule_lang` (
    `id_booking_price_rule_lang` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_booking_price_rule` int(11) UNSIGNED NOT NULL,
    `id_lang` int(11) UNSIGNED NOT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_booking_price_rule_lang`),
 FOREIGN KEY (id_booking_price_rule) references _PREFIX_kb_booking_price_rule(id_booking_price_rule) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_price_rule_shop` (
    `id_booking_price_rule_shop` int(10) unsigned NOT NULL auto_increment,
    `id_booking_price_rule` int(10) unsigned DEFAULT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id_booking_price_rule_shop`),
     FOREIGN KEY (id_booking_price_rule) references _PREFIX_kb_booking_price_rule(id_booking_price_rule) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_facilities` (
    `id_facilities` int(10) unsigned NOT NULL auto_increment,
    `type` enum('room','rent','hotel') NOT NULL DEFAULT 'room',
    `image_type` enum('upload','font') NOT NULL DEFAULT 'upload',
    `upload_image_path` text null,
    `upload_image` text null,
    `font_awesome_icon` text null,
    `active` TINYINT(1) NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_facilities`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_facilities_lang` (
    `id_facilities_lang` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_facilities` int(11) UNSIGNED NOT NULL,
    `id_lang` int(11) UNSIGNED NOT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id_facilities_lang`),
 FOREIGN KEY (id_facilities) references _PREFIX_kb_booking_facilities(id_facilities) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_facilities_shop` (
    `id_facilities_shop` int(10) unsigned NOT NULL auto_increment,
    `id_facilities` int(10) unsigned DEFAULT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id_facilities_shop`),
    FOREIGN KEY (id_facilities) references _PREFIX_kb_booking_facilities(id_facilities) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_product` (
    `id_booking_product` int(10) unsigned NOT NULL auto_increment,
    `id_product` int(10) unsigned NOT NULL DEFAULT '0',
    `product_type` enum('appointment','daily_rental','hotel_booking', 'hourly_rental') NOT NULL DEFAULT 'appointment',
--     `stock_status` enum('0','1') NOT NULL DEFAULT '0',
    `service_type` enum('branch','home_service') NOT NULL DEFAULT 'branch',
    `period_type` enum('date','date_time') NOT NULL DEFAULT 'date',
    `quantity` int(11) unsigned NOT NULL DEFAULT '0',
    `price`  decimal(15,2)  DEFAULT NULL,
--     `start_date`  date DEFAULT NULL,
--     `end_date`  date DEFAULT NULL,
    `min_hours`  int(10) unsigned NOT NULL DEFAULT '0',
    `max_hours`  int(10) unsigned NOT NULL DEFAULT '0',
    `min_days`  int(10) unsigned NOT NULL DEFAULT '0',
    `max_days`  int(10) unsigned NOT NULL DEFAULT '0',
    `star_rating`  int(10) unsigned NOT NULL DEFAULT '0',
    `enable_product_map`  TINYINT(1) NOT NULL DEFAULT '0',
    `date_details` mediumtext null,
    `address`  text NULL,
    `longitude`   varchar(255) NULL,
    `latitude`   varchar(255) NULL,
    `disable_days` text NULL,
    `active` TINYINT(1) NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_booking_product`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_product_shop` (
    `id_booking_product_shop` int(10) unsigned NOT NULL auto_increment,
    `id_booking_product` int(10) unsigned DEFAULT NULL,
    `id_shop` int(10) unsigned DEFAULT NULL,
    PRIMARY KEY (`id_booking_product_shop`),
    FOREIGN KEY (id_booking_product) references _PREFIX_kb_booking_product(id_booking_product) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_product_facilities_mapping` (
    `id_booking_facilities_map` int(10) unsigned NOT NULL auto_increment,
    `id_booking_product` int(10) unsigned NOT NULL DEFAULT '0',
    `id_facilities` int(10) unsigned NOT NULL DEFAULT '0',
     PRIMARY KEY (`id_booking_facilities_map`),
    FOREIGN KEY (id_facilities) references _PREFIX_kb_booking_facilities(id_facilities) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_product_room_facilities_mapping` (
    `id_booking_room_facilities_map` int(10) unsigned NOT NULL auto_increment,
    `id_booking_product` int(10) unsigned NOT NULL DEFAULT '0',
--     `id_room` int(10) unsigned NOT NULL DEFAULT '0',
    `id_room_category` int(10) unsigned NOT NULL DEFAULT '0',
    `id_room_type` int(10) unsigned NOT NULL DEFAULT '0',
    `room_quantity` int(10) unsigned NOT NULL DEFAULT '0',
    `price` decimal(15,2)  DEFAULT NULL,
    `start_time` text null,
    `end_time` text null,
    `id_facilities` text null,
    `upload_images` text null,
    `active` TINYINT(1) NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_booking_room_facilities_map`),
    FOREIGN KEY (id_booking_product) references _PREFIX_kb_booking_product(id_booking_product) ON DELETE CASCADE
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_product_cart` (
    `id_booking_cart` int(10) unsigned NOT NULL auto_increment,
    `id_cart` int(10) unsigned NOT NULL DEFAULT '0',
--     `id_order` int(10) unsigned NOT NULL DEFAULT '0',
    `id_product` int(10) unsigned NOT NULL DEFAULT '0',
    `id_customization` int(10) unsigned NOT NULL DEFAULT '0',
    `product_type` varchar(255) NULL,
    `id_room` int(10) unsigned NOT NULL DEFAULT '0',
    `check_in` datetime not null,
    `check_out` datetime not null,
    `price` decimal(15,2)  DEFAULT NULL,
    `qty` int(10) unsigned NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_booking_cart`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `_PREFIX_kb_booking_product_order` (
    `id_booking_order` int(10) unsigned NOT NULL auto_increment,
    `id_cart` int(10) unsigned NOT NULL DEFAULT '0',
    `id_order` int(10) unsigned NOT NULL DEFAULT '0',
    `id_product` int(10) unsigned NOT NULL DEFAULT '0',
    `id_customization` int(10) unsigned NOT NULL DEFAULT '0',
    `price` decimal(15,2)  DEFAULT NULL,
    `qty` int(10) unsigned NOT NULL DEFAULT '0',
    `date_add` datetime NOT NULL,
    `date_upd` datetime NOT NULL,
    PRIMARY KEY (`id_booking_order`)
) ENGINE=ENGINE_TYPE  DEFAULT CHARSET=utf8;