CREATE TABLE IF NOT EXISTS `scripts` (
  `id` tinyint(4) NOT NULL auto_increment,
  `scriptname` varchar(50) default NULL,
  `homepage` varchar(50) default NULL,
  `description` text,
  `filetype` varchar(15) default NULL,
  `fileinfo` varchar(200) default NULL,
  `scriptdirtocopy` varchar(50) default NULL,
  `commandsaftercopy` text,
  `customfileownerships` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='ehcp db - stores scripts that can be installed in ehcp';

INSERT INTO `scripts` (`id`, `scriptname`, `homepage`, `description`, `filetype`, `fileinfo`, `scriptdirtocopy`, `commandsaftercopy`,customfileownerships) VALUES
(1, 'cms- joomla - 1.5.4', NULL, NULL, 'directurl', 'http://www.joomlatr.org/downloads/Joomla_1.5.5-Stable-Full_Package_tr.zip', '', 'cp /dev/null configuration.php\r\nchmod a+w configuration.php',''),
(2, 'cms- joomla - 2.5', NULL, NULL, 'directurl', 'http://downloads.joomlacode.org/frsrelease/7/6/0/76012/Joomla_2.5.7-Stable-Full_Package.zip', '', 'cp /dev/null configuration.php\r\nchmod a+w configuration.php',''),
(3, 'phpbb Forum script-3.02-tr', NULL, NULL, 'directurl', 'http://download.phpbbturkey.net/phpBB-3.0.2-tr.zip', 'phpBB3', 'chmod a+w cache\r\nchmod a+w files\r\nchmod a+w store\r\nchmod a+w config.php\r\nchmod a+w images/avatars/upload/\r\n',''),
(4, 'cms - drupal 6.6', NULL, NULL, 'directurl', 'http://ftp.drupal.org/files/projects/drupal-6.6.tar.gz', 'drupal-6.6', 'cp ./sites/default/default.settings.php  ./sites/default/settings.php\r\n\r\nchmod -Rvf a+w ./sites/default\r\n',''),
(5, 'CMS - xoops 2.0.18.1', NULL, NULL, 'directurl', 'http://switch.dl.sourceforge.net/sourceforge/xoops/xoops-2.0.18.1.tar.gz', 'xoops-2.0.18.1/htdocs', NULL,''),
(6, 'netoffice Online Project Management ', NULL, NULL, 'directurl', 'http://download.ehcp.net/netoffice.dailybuild.20080927.zip', 'netoffice', NULL,''),
(7, 'phpmyadmin 3.0', NULL, NULL, 'directurl', 'http://heanet.dl.sourceforge.net/sourceforge/phpmyadmin/phpMyAdmin-3.0.0-all-languages.zip', 'phpMyAdmin-3.0.0-all-languages', NULL,''),
(8, 'blog - b2evolution 2.4.5', NULL, NULL, 'directurl', 'http://downloads.sourceforge.net/evocms/b2evolution-2.4.5-stable-2008-09-17.zip', 'b2evolution/blogs', NULL,''),
(9, 'cms - wordpress', NULL, NULL, 'directurl', 'http://wordpress.org/latest.tar.gz', 'wordpress', NULL,''),
(10, 'phpfreechat', NULL, NULL, 'directurl', 'http://mesh.dl.sourceforge.net/sourceforge/phpfreechat/phpfreechat-1.2.tar.gz', 'phpfreechat-1.2', NULL,''),
(11, 'cms - modx ', NULL, NULL, 'directurl', 'http://download.ehcp.net/modx-0.9.6.2.tar.gz', 'modx-0.9.6.2', NULL,''),
(12, 'cms - e107', NULL, NULL, 'directurl', 'http://mesh.dl.sourceforge.net/sourceforge/e107/e107_v0.7.13_full.tar.gz', '', 'chmod a+w e107_config.php\r\nchmod a+w e107_files/cache/\r\nchmod a+w e107_files/public/\r\nchmod a+w e107_files/public/avatars/\r\nchmod a+w e107_plugins/\r\nchmod a+w e107_themes/',''),
(13, 'cms - tikiwikicms - Huge !', NULL, NULL, 'directurl', 'http://garr.dl.sourceforge.net/sourceforge/tikiwiki/tikiwiki-2.2.tar.bz2', 'tikiwiki-2.2', NULL,''),
(14, 'osCommerce', '', NULL, 'directurl', 'http://www.oscommerce.com/ext/oscommerce-2.2rc2a.zip', 'oscommerce-2.2rc2a/catalog', 'chmod a+w includes/configure.php\r\nchmod a+w admin/includes/configure.php \r\n',''),
(15, 'Gallery - Coppermine', NULL, NULL, 'directurl', 'http://garr.dl.sourceforge.net/sourceforge/coppermine/cpg1.4.19.tar.bz2', 'cpg1419', NULL,''),
(16, 'cms - cmsmadesimple', NULL, NULL, 'directurl', 'http://dev.cmsmadesimple.org/frs/download.php/2536/cmsmadesimple-1.4.1-full.tar.gz', '', NULL,''),
(17, 'cms - e-xoops', NULL, NULL, 'directurl', 'http://www.seslisevdasi.com/e-xoops_1.05Rev3.zip', 'exoops', 'chmod a+w mainfile.php\r\n\r\nchmod 777 cache/system\r\nchmod 666 images/verify.png\r\nchmod 777 images/smilies\r\nchmod 666 images/smilies/icon_biggrin.gif\r\nchmod 666 images/smilies/icon_confused.gif\r\nchmod 666 images/smilies/icon_cool.gif\r\nchmod 666 images/smilies/icon_cry.gif\r\nchmod 666 images/smilies/icon_drink.gif\r\nchmod 666 images/smilies/icon_eek.gif\r\nchmod 666 images/smilies/icon_evil.gif\r\nchmod 666 images/smilies/icon_frown.gif\r\nchmod 666 images/smilies/icon_hammer.gif\r\nchmod 666 images/smilies/icon_idea.gif\r\nchmod 666 images/smilies/icon_lol.gif\r\nchmod 666 images/smilies/icon_mad.gif\r\nchmod 666 images/smilies/icon_razz.gif\r\nchmod 666 images/smilies/icon_redface.gif\r\nchmod 666 images/smilies/icon_rolleyes.gif\r\nchmod 666 images/smilies/icon_smile.gif\r\nchmod 666 images/smilies/icon_wink.gif\r\nchmod 777 images/ranks\r\nchmod 666 images/ranks/moderator.gif\r\nchmod 666 images/ranks/rank0.gif\r\nchmod 666 images/ranks/rank1.gif\r\nchmod 666 images/ranks/rank2.gif\r\nchmod 666 images/ranks/rank3.gif\r\nchmod 666 images/ranks/rank4.gif\r\nchmod 666 images/ranks/rank5.gif\r\nchmod 666 images/ranks/webmaster.gif\r\nchmod 777 images/library\r\nchmod 777 images/avatar\r\nchmod 666 images/avatar/001.gif\r\nchmod 666 images/avatar/002.gif\r\nchmod 666 images/avatar/003.gif\r\nchmod 666 images/avatar/004.gif\r\nchmod 666 images/avatar/005.gif\r\nchmod 666 images/avatar/006.gif\r\nchmod 666 images/avatar/033.gif\r\nchmod 666 images/avatar/034.gif\r\nchmod 666 images/avatar/036.gif\r\nchmod 666 images/avatar/037.gif\r\nchmod 666 images/avatar/039.gif\r\nchmod 666 images/avatar/040.gif\r\nchmod 666 images/avatar/043.gif\r\nchmod 666 images/avatar/044.gif\r\nchmod 666 images/avatar/045.gif\r\nchmod 666 images/avatar/046.gif\r\nchmod 666 images/avatar/047.gif\r\nchmod 666 images/avatar/048.gif\r\nchmod 666 images/avatar/049.gif\r\nchmod 666 images/avatar/050.gif\r\nchmod 666 images/avatar/052.gif\r\nchmod 666 images/avatar/056.gif\r\nchmod 666 images/avatar/075.gif\r\nchmod 666 images/avatar/088.gif\r\nchmod 666 images/avatar/091.gif\r\nchmod 666 images/avatar/093.gif\r\nchmod 666 images/avatar/095.gif\r\nchmod 666 images/avatar/096.gif\r\nchmod 666 images/avatar/097.gif\r\nchmod 666 images/avatar/099.gif\r\nchmod 666 images/avatar/100.gif\r\nchmod 666 images/avatar/102.gif\r\nchmod 666 images/avatar/103.gif\r\nchmod 666 images/avatar/104.gif\r\nchmod 666 images/avatar/105.gif\r\nchmod 666 images/avatar/122.gif\r\nchmod 666 images/avatar/124.gif\r\nchmod 666 images/avatar/125.gif\r\nchmod 666 images/avatar/126.gif\r\nchmod 666 images/avatar/127.gif\r\nchmod 666 images/avatar/128.gif\r\nchmod 666 images/avatar/129.gif\r\nchmod 666 images/avatar/130.gif\r\nchmod 666 images/avatar/131.gif\r\nchmod 666 images/avatar/132.gif\r\nchmod 666 images/avatar/133.gif\r\nchmod 666 images/avatar/134.gif\r\nchmod 666 images/avatar/135.gif\r\nchmod 666 images/avatar/136.gif\r\nchmod 666 images/avatar/137.gif\r\nchmod 666 images/avatar/138.gif\r\nchmod 666 images/avatar/139.gif\r\nchmod 666 images/avatar/140.gif\r\nchmod 666 images/avatar/141.gif\r\nchmod 666 images/avatar/142.gif\r\nchmod 666 images/avatar/143.gif\r\nchmod 666 images/avatar/144.gif\r\nchmod 666 images/avatar/145.gif\r\nchmod 666 images/avatar/146.gif\r\nchmod 666 images/avatar/147.gif\r\nchmod 666 images/avatar/148.gif\r\nchmod 666 images/avatar/149.gif\r\nchmod 666 images/avatar/150.gif\r\nchmod 666 images/avatar/151.gif\r\nchmod 666 images/avatar/152.gif\r\nchmod 666 images/avatar/153.gif\r\nchmod 666 images/avatar/154.gif\r\nchmod 666 images/avatar/155.gif\r\nchmod 666 images/avatar/156.gif\r\nchmod 666 images/avatar/157.gif\r\nchmod 666 images/avatar/158.gif\r\nchmod 666 images/avatar/159.gif\r\nchmod 666 images/avatar/160.gif\r\nchmod 666 images/avatar/161.gif\r\nchmod 666 images/avatar/162.gif\r\nchmod 666 images/avatar/163.gif\r\nchmod 666 images/avatar/164.gif\r\nchmod 666 images/avatar/165.gif\r\nchmod 666 images/avatar/166.gif\r\nchmod 666 images/avatar/167.gif\r\nchmod 666 images/avatar/168.gif\r\nchmod 666 images/avatar/169.gif\r\nchmod 666 images/avatar/170.gif\r\nchmod 666 images/avatar/171.gif\r\nchmod 666 images/avatar/172.gif\r\nchmod 666 images/avatar/173.gif\r\nchmod 666 images/avatar/174.gif\r\nchmod 666 images/avatar/175.gif\r\nchmod 666 images/avatar/176.gif\r\nchmod 666 images/avatar/177.gif\r\nchmod 666 images/avatar/178.gif\r\nchmod 666 images/avatar/179.gif\r\nchmod 666 images/avatar/180.gif\r\nchmod 666 images/avatar/181.gif\r\nchmod 666 images/avatar/182.gif\r\nchmod 666 images/avatar/183.gif\r\nchmod 666 images/avatar/184.gif\r\nchmod 666 images/avatar/185.gif\r\nchmod 666 images/avatar/186.gif\r\nchmod 666 images/avatar/187.gif\r\nchmod 666 images/avatar/188.gif\r\nchmod 666 images/avatar/189.gif\r\nchmod 666 images/avatar/190.gif\r\nchmod 666 images/avatar/191.gif\r\nchmod 666 images/avatar/192.gif\r\nchmod 666 images/avatar/193.gif\r\nchmod 666 images/avatar/194.gif\r\nchmod 666 images/avatar/195.gif\r\nchmod 666 images/avatar/196.gif\r\nchmod 666 images/avatar/197.gif\r\nchmod 666 images/avatar/198.gif\r\nchmod 666 images/avatar/199.gif\r\nchmod 666 images/avatar/200.gif\r\nchmod 666 images/avatar/201.gif\r\nchmod 666 images/avatar/202.gif\r\nchmod 666 images/avatar/203.gif\r\nchmod 666 images/avatar/204.gif\r\nchmod 666 images/avatar/205.gif\r\nchmod 666 images/avatar/206.gif\r\nchmod 666 images/avatar/207.gif\r\nchmod 666 images/avatar/208.gif\r\nchmod 666 images/avatar/209.gif\r\nchmod 666 images/avatar/210.gif\r\nchmod 666 images/avatar/211.gif\r\nchmod 666 images/avatar/212.gif\r\nchmod 666 images/avatar/213.gif\r\nchmod 666 images/avatar/214.gif\r\nchmod 666 images/avatar/215.gif\r\nchmod 666 images/avatar/216.gif\r\nchmod 666 images/avatar/217.gif\r\nchmod 666 images/avatar/218.gif\r\nchmod 666 images/avatar/219.gif\r\nchmod 666 images/avatar/220.gif\r\nchmod 666 images/avatar/221.gif\r\nchmod 666 images/avatar/222.gif\r\nchmod 666 images/avatar/223.gif\r\nchmod 666 images/avatar/224.gif\r\nchmod 666 images/avatar/225.gif\r\nchmod 666 images/avatar/226.gif\r\nchmod 666 images/avatar/227.gif\r\nchmod 666 images/avatar/228.gif\r\nchmod 666 images/avatar/229.gif\r\nchmod 666 images/avatar/230.gif\r\nchmod 666 images/avatar/231.gif\r\nchmod 666 images/avatar/232.gif\r\nchmod 666 images/avatar/233.gif\r\nchmod 666 images/avatar/234.gif\r\nchmod 666 images/avatar/235.gif\r\nchmod 666 images/avatar/236.gif\r\nchmod 666 images/avatar/237.gif\r\nchmod 666 images/avatar/238.gif\r\nchmod 666 images/avatar/239.gif\r\nchmod 666 images/avatar/240.gif\r\nchmod 666 images/avatar/241.gif\r\nchmod 666 images/avatar/242.gif\r\nchmod 666 images/avatar/243.gif\r\nchmod 666 images/avatar/blank.gif\r\nchmod 777 images/avatar/users\r\nchmod 777 images/avatar/smilies\r\nchmod 666 images/avatar/smilies/007.gif\r\nchmod 666 images/avatar/smilies/008.gif\r\nchmod 666 images/avatar/smilies/009.gif\r\nchmod 666 images/avatar/smilies/010.gif\r\nchmod 666 images/avatar/smilies/011.gif\r\nchmod 666 images/avatar/smilies/012.gif\r\nchmod 666 images/avatar/smilies/013.gif\r\nchmod 666 images/avatar/smilies/014.gif\r\nchmod 666 images/avatar/smilies/015.gif\r\nchmod 666 images/avatar/smilies/016.gif\r\nchmod 666 images/avatar/smilies/017.gif\r\nchmod 666 images/avatar/smilies/018.gif\r\nchmod 666 images/avatar/smilies/019.gif\r\nchmod 666 images/avatar/smilies/020.gif\r\nchmod 666 images/avatar/smilies/021.gif\r\nchmod 666 images/avatar/smilies/022.gif\r\nchmod 666 images/avatar/smilies/023.gif\r\nchmod 666 images/avatar/smilies/024.gif\r\nchmod 666 images/avatar/smilies/025.gif\r\nchmod 666 images/avatar/smilies/026.gif\r\nchmod 666 images/avatar/smilies/027.gif\r\nchmod 666 images/avatar/smilies/028.gif\r\nchmod 666 images/avatar/smilies/029.gif\r\nchmod 666 images/avatar/smilies/030.gif\r\nchmod 666 images/avatar/smilies/031.gif\r\nchmod 666 images/avatar/smilies/032.gif\r\nchmod 666 images/avatar/smilies/035.gif\r\nchmod 666 images/avatar/smilies/038.gif\r\nchmod 666 images/avatar/smilies/041.gif\r\nchmod 666 images/avatar/smilies/042.gif\r\nchmod 666 images/avatar/smilies/053.gif\r\nchmod 666 images/avatar/smilies/054.gif\r\nchmod 666 images/avatar/smilies/057.gif\r\nchmod 666 images/avatar/smilies/059.gif\r\nchmod 666 images/avatar/smilies/060.gif\r\nchmod 666 images/avatar/smilies/061.gif\r\nchmod 666 images/avatar/smilies/062.gif\r\nchmod 666 images/avatar/smilies/063.gif\r\nchmod 666 images/avatar/smilies/064.gif\r\nchmod 666 images/avatar/smilies/065.gif\r\nchmod 666 images/avatar/smilies/066.gif\r\nchmod 666 images/avatar/smilies/067.gif\r\nchmod 666 images/avatar/smilies/068.gif\r\nchmod 666 images/avatar/smilies/069.gif\r\nchmod 666 images/avatar/smilies/070.gif\r\nchmod 666 images/avatar/smilies/071.gif\r\nchmod 666 images/avatar/smilies/072.gif\r\nchmod 666 images/avatar/smilies/073.gif\r\nchmod 666 images/avatar/smilies/074.gif\r\nchmod 666 images/avatar/smilies/076.gif\r\nchmod 666 images/avatar/smilies/077.gif\r\nchmod 666 images/avatar/smilies/078.gif\r\nchmod 666 images/avatar/smilies/079.gif\r\nchmod 666 images/avatar/smilies/080.gif\r\nchmod 666 images/avatar/smilies/081.gif\r\nchmod 666 images/avatar/smilies/082.gif\r\nchmod 666 images/avatar/smilies/083.gif\r\nchmod 666 images/avatar/smilies/084.gif\r\nchmod 666 images/avatar/smilies/085.gif\r\nchmod 666 images/avatar/smilies/086.gif\r\nchmod 666 images/avatar/smilies/087.gif\r\nchmod 666 images/avatar/smilies/092.gif\r\nchmod 666 images/avatar/smilies/094.gif\r\nchmod 666 images/avatar/smilies/098.gif\r\nchmod 666 images/avatar/smilies/106.gif\r\nchmod 666 images/avatar/smilies/108.gif\r\nchmod 666 images/avatar/smilies/109.gif\r\nchmod 666 images/avatar/smilies/110.gif\r\nchmod 666 images/avatar/smilies/111.gif\r\nchmod 666 images/avatar/smilies/112.gif\r\nchmod 666 images/avatar/smilies/113.gif\r\nchmod 666 images/avatar/smilies/114.gif\r\nchmod 666 images/avatar/smilies/115.gif\r\nchmod 666 images/avatar/smilies/116.gif\r\nchmod 666 images/avatar/smilies/117.gif\r\nchmod 666 images/avatar/smilies/118.gif\r\nchmod 666 images/avatar/smilies/119.gif\r\nchmod 666 images/avatar/smilies/120.gif\r\nchmod 666 images/avatar/smilies/121.gif\r\nchmod 777 modules/headlines/cache\r\nchmod 666 modules/mydownloads/cache/downloads.xml\r\nchmod 666 modules/mydownloads/cache/config.php\r\nchmod 666 modules/mydownloads/cache/disclaimer.php\r\nchmod 777 modules/mydownloads/cache/files\r\nchmod 777 modules/mydownloads/cache/logos\r\nchmod 777 modules/mydownloads/cache/shots\r\nchmod 666 modules/mylinks/cache/links.xml\r\nchmod 666 modules/mylinks/cache/config.php\r\nchmod 666 modules/mylinks/cache/disclaimer.php\r\nchmod 777 modules/mylinks/cache/logos\r\nchmod 777 modules/mylinks/cache/shots\r\nchmod 666 modules/newbb/cache/config.php\r\nchmod 666 modules/newbb/cache/disclaimer.php\r\nchmod 666 modules/news/cache/config.php\r\nchmod 666 modules/news/cache/news.xml\r\nchmod 777 modules/news/cache/topics\r\nchmod 666 modules/news/cache/topics/AllTopics.gif\r\nchmod 666 modules/news/cache/topics/announces.gif\r\nchmod 666 modules/news/cache/topics/blank.gif\r\nchmod 666 modules/news/cache/topics/blocks.gif\r\nchmod 666 modules/news/cache/topics/bug.gif\r\nchmod 666 modules/news/cache/topics/doc.gif\r\nchmod 666 modules/news/cache/topics/e-xoops.gif\r\nchmod 666 modules/news/cache/topics/graphics.gif\r\nchmod 666 modules/news/cache/topics/hacks.gif\r\nchmod 666 modules/news/cache/topics/hardware.gif\r\nchmod 666 modules/news/cache/topics/ideas.gif\r\nchmod 666 modules/news/cache/topics/info.gif\r\nchmod 666 modules/news/cache/topics/links.gif\r\nchmod 666 modules/news/cache/topics/misc.gif\r\nchmod 666 modules/news/cache/topics/modules.gif\r\nchmod 666 modules/news/cache/topics/money.gif\r\nchmod 666 modules/news/cache/topics/multimedia.gif\r\nchmod 666 modules/news/cache/topics/news.gif\r\nchmod 666 modules/news/cache/topics/patches.gif\r\nchmod 666 modules/news/cache/topics/privacy.gif\r\nchmod 666 modules/news/cache/topics/programming.gif\r\nchmod 666 modules/news/cache/topics/questions.gif\r\nchmod 666 modules/news/cache/topics/security.gif\r\nchmod 666 modules/news/cache/topics/software.gif\r\nchmod 666 modules/news/cache/topics/themes.gif\r\nchmod 666 modules/partners/cache/config.php\r\nchmod 777 modules/partners/cache/images\r\nchmod 777 modules/phpRPC/cache/logs\r\nchmod 666 modules/phpRPC/cache/config.php\r\nchmod 666 modules/sections/cache/sections.xml\r\nchmod 666 modules/sections/cache/intro.php\r\nchmod 666 modules/sections/cache/config.php\r\nchmod 777 modules/sections/cache/images\r\nchmod 666 modules/system/cache/adminmenu.php\r\nchmod 666 modules/system/cache/badips.php\r\nchmod 666 modules/system/cache/badunames.php\r\nchmod 666 modules/system/cache/bademails.php\r\nchmod 666 modules/system/cache/badwords.php\r\nchmod 666 modules/system/cache/config.php\r\nchmod 666 modules/system/cache/footer.php\r\nchmod 666 modules/system/cache/header.php\r\nchmod 666 modules/system/cache/meta.php\r\nchmod 666 modules/system/cache/unwanted.php\r\nchmod 666 modules/system/cache/wanted.php\r\nchmod 777 modules/xoopsfaq/cache\r\nchmod 666 modules/xoopsfaq/cache/config.php\r\nchmod 666 modules/xoopsfaq/cache/faq.xml\r\n\r\nchmod a+x modules/system/cache',''),
(18, 'cms - MemHT 4.01', NULL, NULL, 'directurl', 'http://www.memht.com/files/memht_portal/releases/MemHT_Portal_4.0.1.rar', 'MemHT_Portal_4.0.1/files', NULL,''),
(19, 'Mediawiki 1.13.2', NULL, NULL, 'directurl', 'http://download.wikimedia.org/mediawiki/1.13/mediawiki-1.13.2.tar.gz', 'mediawiki-1.13.2', NULL,''),
(20, 'ecommerce-Magento', NULL, NULL, 'directurl', 'http://download.ehcp.net/magento-downloader-1.1.3.tar.bz2', 'magento', 'chmod a+w *',''),
(21, 'osTicket 1.7', NULL, NULL, 'directurl', 'http://osticket.com/dl.php?f=osTicket-1.7.0.zip', 'upload', 'chmod a+w ostconfig.php\r\nchmod a+w include/settings.php',''),
(22, 'forum - smf 1.1.7', NULL, NULL, 'directurl', 'http://mirror.pdx.simplemachines.org/downloads/smf_1-1-7_install.tar.gz', '', 'chmod a+w attachments\r\nchmod a+w avatars\r\nchmod a+w Packages\r\nchmod a+w Packages/installed.list\r\nchmod a+w Smileys\r\nchmod a+w Themes\r\nchmod a+w agreement.txt\r\nchmod a+w Settings.php\r\nchmod a+w Settings_bak.php\r\n',''),
(23, 'ecommerce - ZenCart', NULL, NULL, 'directurl', 'http://dfn.dl.sourceforge.net/sourceforge/zencart/zen-cart-v1.3.8a-full-fileset-12112007.zip', 'zen-cart-v1.3.8a-full-fileset-12112007', 'cp /dev/null includes/configure.php\r\ncp /dev/null admin/includes/configure.php\r\n\r\nchmod a+w includes/configure.php\r\nchmod a+w admin/includes/configure.php\r\n\r\nchmod a+w cache\r\nchmod a+w -Rf images\r\nchmod a+w -Rf includes/languages/english/html_includes\r\nchmod a+w -Rf media\r\nchmod a+w -Rf pub\r\nchmod a+w -Rf admin/backups\r\nchmod a+w -Rf admin/images/graphs\r\n',''),
(24, 'Gallery 2.3', NULL, NULL, 'directurl', 'http://puzzle.dl.sourceforge.net/sourceforge/gallery/gallery-2.3-typical.tar.gz', 'gallery2', NULL,''),
(25, 'course - moodle 1.9.5', NULL, NULL, 'directurl', 'http://download.moodle.org/stable19/moodle-1.9.5.tgz', 'moodle', 'mkdir moodledata\r\nchmod a+w moodledata',''),
(26, 'phpAuction 3.3 gpl', NULL, NULL, 'directurl', 'http://download.ehcp.net/phpauction-gpl-3.3.tar.gz', 'phpauction-gpl-3.3', 'chmod 666 includes/config.inc.php\r\nchmod 666 includes/passwd.inc.php\r\nchmod 666 includes/categories_select_box.ES.inc.php\r\nchmod 666 includes/categories_select_box.EN.inc.php\r\nchmod 666 includes/countries.inc.php\r\nchmod 777 uploaded\r\nchmod 777 uploaded/cache\r\n',''),
(27, 'Crafty Syntax Live Help', NULL, NULL, 'directurl', 'http://dfn.dl.sourceforge.net/sourceforge/cslive/craftysyntax.2.15.0.tar.gz', 'craftysyntax2.15.0', 'chmod 777 config.php',''),
(28, 'openrealty 2.5.5', NULL, NULL, 'directurl', 'http://open-realty.org/release/open-realty2.5.5.zip', '', 'chmod a+w include/common.dist.php\r\nchmod a+w include\r\nchmod a+w images/listing_photos\r\nchmod a+w images/user_photos\r\nchmod a+w images/vtour_photos\r\nchmod a+w images/page_upload\r\nchmod a+w files/listings\r\nchmod a+w files/users',''),
(29, 'ehcp webmail', NULL, NULL, 'directurl', 'http://www.ehcp.net/other/webmail.tgz', 'webmail', NULL,''),
(30, 'ehcp itself', NULL, NULL, 'directurl', 'http://www.ehcp.net/download', 'ehcp', 'cp -rvf /var/www/vhosts/ehcp/config.php ./',''),
(31, 'phpmotion', '', 'video sharing cms-youtube like-requires many additional settings', 'directurl', 'http://downloads.phpmotion.com/V2.1/PHP5.zip', 'PHP5/PHPmotion', NULL,''),
(32, 'clip-bucket', 'http://clip-bucket.com', 'video sharing script like youtube', 'directurl', 'http://clip-bucket.com/files/downloads/2009/09/cb_1.7.1_r706.rar', '', NULL,''),
(33, 'lime survey', 'http://www.limesurvey.org', 'survey script', 'directurl', 'http://garr.dl.sourceforge.net/project/limesurvey/1._LimeSurvey_stable/1.90%2B/limesurvey190plus-build9642-20101214.tar.gz', '', NULL,''),
(34, 'php-calender', 'http://www.php-calendar.com', 'calendar', 'directurl', 'http://php-calendar.googlecode.com/files/php-calendar-2.0-beta9.tar.gz', 'php-calendar-2.0-beta9', NULL,''),
(35, 'symphony cms', 'http://symphony-cms.com', 'cms', 'directurl', 'http://downloads.symphony-cms.com/global-asset-download/symphony-package/60649/symphony2.2.1.zip/', 'symphony-2', NULL,''),
(36, 'sitracker - helpdesk', 'http://sitracker.org', 'helpdesk-ticket', 'directurl', 'http://garr.dl.sourceforge.net/project/sitracker/stable/3.63/sit_3.63p1.tar.gz', 'sit-3.63', NULL,''),
(37, 'chive - mysql admin', 'http://launchpad.net/chive', 'mysql administration', 'directurl', 'http://launchpad.net/chive/0.5/0.5.1/+download/chive_0.5.1.tar.gz', 'chive', NULL,''),
(38, 'sqlbuddy - mysql admin', 'http://www.sqlbuddy.com', 'mysql administration', 'directurl', 'http://www.sqlbuddy.com/download/dl.php', 'sqlbuddy', NULL,''),
(39, 'mybb forum', 'http://www.mybb.com', 'forum script/program', 'directurl', 'http://cloud.github.com/downloads/mybb/mybb16/mybb_1608.zip', 'Upload', NULL,''),
(40, 'Collabtive', 'http://collabtive.o-dyn.de', 'project management-groupware script/program', 'directurl', 'http://garr.dl.sourceforge.net/project/collabtive/collabtive/0.7.6/collabtive076.zip', '', 'chmod a+w templates_c',''),
(41, 'webid', 'http://www.webidsupport.com', 'auction program', 'directurl', 'http://garr.dl.sourceforge.net/project/simpleauction/simpleauction/WeBid%20v1.0.6/WeBid-1.0.6.zip', 'WeBid', 'cp includes/config.inc.php.new includes/config.inc.php\r\nchmod a+w cache\r\nchmod a+w uploaded\r\nchmod a+w uploaded/banners\r\nchmod a+w uploaded/cache\r\nchmod a+w includes/config.inc.php\r\nchmod a+w includes/countries.inc.php\r\nchmod a+w includes/currencies.php\r\nchmod a+w includes/membertypes.inc.php\r\nchmod a+w language/EN/categories.inc.php\r\nchmod a+w language/EN/categories_select_box.inc.php', NULL),
(42, 'opencart', 'http://www.opencart.com', 'shopping cart, e-commerce program', 'directurl', 'https://codeload.github.com/opencart/opencart/zip/v1.5.5.1', 'opencart-1.5.5.1/upload', '', NULL),
(43, 'prestashop', 'http://www.prestashop.com', 'shopping cart, e-commerce program', 'directurl', 'http://www.prestashop.com/ajax/controller.php?method=download&type=releases&file=prestashop_1.5.4.1.zip&language=en', 'prestashop', '', NULL),
(44, 'serendipity', 'http://www.s9y.org', 'blog-cms', 'directurl', 'http://garr.dl.sourceforge.net/project/php-blog/serendipity/1.7/serendipity-1.7.tar.gz', 'serendipity', '', NULL),
(45, 'dotclear', 'http://www.dotclear.org', 'blog', 'directurl', 'http://download.dotclear.org/latest.tar.gz', 'dotclear', '', NULL),
(46, 'textpattern', 'http://www.textpattern.com', 'cms', 'directurl', 'http://textpattern.com/file_download/90/textpattern-4.5.4.zip', 'textpattern-4.5.4', '', NULL),
(47, 'lifetype', 'http://www.lifetype.net', 'blog', 'directurl', 'http://garr.dl.sourceforge.net/project/lifetype/lifetype/lifetype-1.2.11/lifetype-1.2.11.tar.gz', 'lifetype-1.2.11', '', NULL),
(48, 'nucleus', 'http://www.nucleuscms.org', 'cms', 'directurl', 'http://garr.dl.sourceforge.net/project/nucleuscms/1.%20Nucleus%20Core/Nucleus%20v3.65/nucleus3.65.zip', 'nucleus3.65', '', NULL),
(49, 'oxwall', 'http://www.oxwall.org', 'Community-Social Networking software', 'directurl', 'http://ow.download.s3.amazonaws.com/oxwall-1.5.3.zip', '', '', NULL),
(50, 'elgg', 'http://www.elgg.org', 'Community-Social Networking software', 'directurl', 'http://elgg.org/download/elgg-1.8.15.zip', 'elgg-1.8.15', '', NULL),
(51, 'osclass', 'http://www.osclass.org', 'Classified website builder-ad-ecommerce', 'directurl', 'http://garr.dl.sourceforge.net/project/osclass/3.1/osclass.3.1.2.zip', '', '', NULL)
;






CREATE TABLE IF NOT EXISTS scripts_log (
  id tinyint(4) NOT NULL auto_increment,
  host varchar(30) default NULL,
  scriptname varchar(50) default NULL,
  dir text,
  panelusername varchar(30) default NULL,
  domainname varchar(50) default NULL,
  link varchar(200) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM  COMMENT='ehcp db - stores script install logs that are installed through ehcp easy install scripts';


CREATE TABLE IF NOT EXISTS servers (
  id smallint(6) NOT NULL auto_increment,
  servertype varchar(10) default NULL,
  ip varchar(30) default NULL,
  accessip varchar(30) default NULL,
  mandatory char(1) default NULL,
  location varchar(20) default NULL,
  password varchar(20) default NULL,
  defaultmysqlhostname varchar(30) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM COMMENT='ehcp db - servers that are to be used with ehcp, multi server concept';



CREATE TABLE IF NOT EXISTS directories (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  domainname varchar(50) default NULL,
  username varchar(30) default NULL,
  password varchar(30) default NULL,
  directory varchar(100) NOT NULL,
  expire date default NULL,
  comment varchar(50) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - password protected directories';

CREATE TABLE IF NOT EXISTS subdomains (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  subdomain varchar(30) default NULL,
  domainname varchar(50) default NULL,
  homedir varchar(100) default NULL,
  ftpusername varchar(30) default NULL,
  comment varchar(50) default NULL,
  status varchar(10) default NULL,
  password varchar(20) default NULL,
  email varchar(50) default NULL,
  webserverips varchar(200) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - subdomains';

CREATE TABLE IF NOT EXISTS customsettings (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  domainname varchar(50) default NULL,
  name varchar(30) default NULL,
  webservertype varchar(30) default NULL,
  `value` text,
  value2 text,
  comment varchar(50) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - custom http, custom dns for domains';


CREATE TABLE alias (
  host varchar(30) default NULL,
  address varchar(255) NOT NULL default '',
  goto text NOT NULL,
  domain varchar(255) NOT NULL default '',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  modified datetime NOT NULL default '0000-00-00 00:00:00',
  active tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (address),
  KEY address (address)
) ENGINE=MyISAM COMMENT='ehcp db - Postfix Admin - Virtual Aliases';



CREATE TABLE IF NOT EXISTS domains (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  webserverips varchar(200) default NULL,
  dnsserverips varchar(200) default NULL,
  mailserverips varchar(200) default NULL,
  mysqlserverips varchar(200) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  domainname varchar(50) default NULL,
  homedir varchar(100) default NULL,
  comment varchar(50) default NULL,
  status varchar(10) default NULL,
  serverip varchar(30) default NULL,
  diskquotaused int(4) default NULL,  -- Thanks to deconectat
  diskquota int(4) default NULL,
  diskquotaovernotified int(4) NULL,
  diskquotaoversince date NULL ,
  graceperiod int(4) default 7 ,
  apachetemplate text NULL,
  dnstemplate text NULL,
  aliases text NULL,
  apache2template text NULL,
  nginxtemplate text NULL,
  theorder int(11) default NULL,
  dnsmaster varchar(15) default NULL,
  PRIMARY KEY  (id),
  KEY domainname (domainname)
) ENGINE=MyISAM  comment='ehcp db - list of domains and their properties';



CREATE TABLE IF NOT EXISTS emailusers (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  domainname varchar(50) default NULL,
  mailusername varchar(30) default NULL,
  beforeat varchar(30) default NULL,
  password varchar(40) default NULL,
  email varchar(80) NOT NULL default '',
  status varchar(10) NULL default '',
  quota int(10) default '10485760',
  autoreplysubject varchar(100) default NULL,
  autoreplymessage text,
  PRIMARY KEY  (id),
  KEY email (email)
) ENGINE=MyISAM   COMMENT='ehcp db - email users of domains';


CREATE TABLE IF NOT EXISTS forwardings (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  domainname varchar(50) default NULL,
  source varchar(80) NOT NULL default '',
  destination text NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - email forwardings';


CREATE TABLE ftpaccounts (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  ftpusername varchar(30) NOT NULL,
  password varchar(50) NOT NULL,
  domainname varchar(50) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  homedir varchar(100) default NULL,
  status varchar(10) default NULL,
  type varchar(10) default NULL,
  `datetime` datetime default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY ftpusername (ftpusername)
) ENGINE=MyISAM comment='ehcp db - ftp accounts that are used in domains,ehcp,etc, used in vsftpd';


CREATE TABLE html (
  id varchar(30) NOT NULL default '0',
  htmlkodu longtext,
  htmlkodu2 longtext,
  aciklama varchar(50) default NULL,
  grup varchar(20) default NULL,
  PRIMARY KEY  (id),
  KEY id (id)
) ENGINE=MyISAM comment='ehcp db - used in db style of templates, not used much now';


CREATE TABLE IF NOT EXISTS backups (
  id int(11) NOT NULL auto_increment,
  domainname varchar(100) default NULL,
  host varchar(30) default NULL,
  backupname varchar(100) default NULL,
  filename varchar(200) default NULL,
  date datetime default NULL,
  size bigint(20) default NULL,
  status varchar(100) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - list of backups done through ehcp gui';


CREATE TABLE log (
  id int(11) NOT NULL auto_increment,
  tarih datetime default NULL,
  panelusername varchar(50) default NULL,
  notified varchar(5) default NULL,
  ip varchar(30) default NULL,
  log varchar(60) default NULL,
  referrer varchar(100) default NULL,
  url varchar(100) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db -  some log entries, may not be used';


CREATE TABLE log2 (
  id int(11) NOT NULL auto_increment,
  panelusername varchar(30) default NULL,
  referrer varchar(80) default NULL,
  count int(11) default NULL,
  aciklama varchar(30) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - some log entries, may not be used';


CREATE TABLE IF NOT EXISTS misc (
  id int(11) NOT NULL auto_increment,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  name varchar(40) default NULL,
  `value` varchar(200) default NULL,
  longvalue text,
  comment varchar(100) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM COMMENT='ehcp db - Table for misc configruation of ehcp';


INSERT INTO misc (id, name, `value`, longvalue,comment) VALUES
(1, 'dnsip', '83.133.127.19', NULL,''),
(2, 'adminname', 'b', NULL,''),
(3, 'adminemail', 'b', NULL,''),
(5, 'ehcpdir', '/var/www/vhosts/ehcp', NULL,''),
(6, 'banner', '', 'this is banner.. you may write here something using <a href=\\"?op=options\\">server settings</a>\r\n<br><br>',''),
(7, 'defaulttemplate', 'ep-ic', NULL,''),
-- (7, 'defaulttemplate', 'xp5-z7', NULL,''),
(8, 'defaultlanguage', 'en', NULL,''),
(9, 'updatehostsfile', 'on', NULL,''),
(10,'messagetonewuser', 'Dns servers for our server:\r\n...........\r\n\r\n(This will be sent to new users)', '',''),
(11, 'backupdir', '/var/backup', NULL,''),
(12, 'quotaupdateinterval', '6', NULL,'this is in hours, quota will be calculated in this interval'),
(13, 'webservertype', 'apache2', NULL,'apache2 or nginx, (or any other that is supported)'),
(14, 'webservermode', 'nonssl', NULL,'ssl or nonssl. ssl can be problematic, in some cases.recover in case of failure: http://ehcp.net/?q=node/897'),
(15, 'mysqlcharset', 'DEFAULT CHARACTER SET utf8 COLLATE utf8_turkish_ci', NULL,'Default charset/collation for newly added databases'),
(16, 'enablewebstats', 'on',null,'Webalizer web stats'),
(17, 'versionwarningcounter', '5',null,'')

;


CREATE TABLE mysqldb (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  domainname varchar(50) default NULL,
  dbname varchar(30) default NULL,
  aciklama varchar(30) NOT NULL default '',
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - list of mysql databases, related to ehcp';


CREATE TABLE mysqlusers (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  reseller varchar(30) default NULL,
  panelusername varchar(30) default NULL,
  domainname varchar(50) default NULL,
  dbname varchar(30) default NULL,
  dbusername varchar(30) default NULL,
  password varchar(30) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - list of mysql users related to ehcp';


CREATE TABLE operations (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  user varchar(30) default NULL,
  ip varchar(30) default NULL,
  op varchar(50) default NULL,
  status varchar(15) default NULL,
  tarih datetime default NULL,
  try smallint(6) default '0',
  info varchar(200) default NULL,
  info2 varchar(200) default NULL,
  info3 varchar(200) default NULL,
  action varchar(50) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - list of pending/done daemon operations, misc operations.. ';



CREATE TABLE panelusers (
  id int(11) NOT NULL auto_increment,
  host varchar(30) default NULL,
  reseller varchar(30) default NULL,
  domainname varchar(50) default NULL,
  panelusername varchar(30) default NULL,
  password varchar(40) default NULL,
  email varchar(80) NOT NULL default '',
  quota int(20) default '10485760',
  maxdomains int(11) default NULL,
  maxemails int(11) default NULL,
  maxpanelusers int(11) default NULL,
  maxftpusers smallint(6) default NULL,
  maxdbs int(11) default NULL,
  status varchar(10) default NULL,
  name varchar(100) default NULL,
  comment varchar(100) default NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM comment='ehcp db - panel users, clients, resellers';




INSERT INTO panelusers (id, reseller, domainname, panelusername, password, email, quota, maxdomains, maxemails, maxpanelusers, maxftpusers, maxdbs, status, name) VALUES
(1, 'admin', '', 'admin', '81dc9bdb52d04dc20036dbd8313ed055', 'admin@admindomain.com', 0, 50000, 50000, 50000, 50000, 50000, 'active', NULL);

CREATE TABLE transport (
	domainname varchar(128) NOT NULL default '',
	transport varchar(128) NOT NULL default '',
	UNIQUE KEY domainname (domainname)
) ENGINE=MyISAM comment='ehcp db - email transport maps';



DROP TABLE IF EXISTS `hash`;
CREATE TABLE IF NOT EXISTS `hash` (
  `email` varchar(100) COLLATE utf8_turkish_ci NOT NULL DEFAULT 'NULL',
  `hash` varchar(100) COLLATE utf8_turkish_ci DEFAULT NULL,
  KEY `email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci COMMENT='to store password remind hash';

# these are net2ftp tables for logging etc...

DROP TABLE IF EXISTS net2ftp_logAccess;
DROP TABLE IF EXISTS net2ftp_log_access;
CREATE TABLE net2ftp_log_access (id int(10) unsigned NOT NULL auto_increment,date date NOT NULL default '0000-00-00',time time NOT NULL default '00:00:00',remote_addr text NOT NULL,remote_port text NOT NULL,http_user_agent text NOT NULL,page text NOT NULL,datatransfer int(10) unsigned default '0',executiontime mediumint(8) unsigned default '0',ftpserver text NOT NULL,username text NOT NULL,state text NOT NULL,state2 text NOT NULL,screen text NOT NULL,directory text NOT NULL,entry text NOT NULL,http_referer text NOT NULL,KEY index1 (id)) ENGINE=MyISAM;
DROP TABLE IF EXISTS net2ftp_logError;
DROP TABLE IF EXISTS net2ftp_log_error;
CREATE TABLE net2ftp_log_error (date date NOT NULL default '0000-00-00',time time NOT NULL default '00:00:00',ftpserver text NOT NULL,username text NOT NULL,message text NOT NULL,backtrace text NOT NULL,state text NOT NULL,state2 text NOT NULL,directory text NOT NULL,remote_addr text NOT NULL,remote_port text NOT NULL,http_user_agent text NOT NULL,KEY index1 (date,time,ftpserver(100),username(50))) ENGINE=MyISAM;
DROP TABLE IF EXISTS net2ftp_logConsumptionFtpserver;
DROP TABLE IF EXISTS net2ftp_log_consumption_ftpserver;
CREATE TABLE net2ftp_log_consumption_ftpserver(date date NOT NULL default '0000-00-00',ftpserver varchar(255) NOT NULL default '0',datatransfer int(10) unsigned default '0',executiontime mediumint(8) unsigned default '0',PRIMARY KEY  (date,ftpserver)) ENGINE=MyISAM;
DROP TABLE IF EXISTS net2ftp_logConsumptionIpaddress;
DROP TABLE IF EXISTS net2ftp_log_consumption_ipaddress;
CREATE TABLE net2ftp_log_consumption_ipaddress(date date NOT NULL default '0000-00-00',ipaddress varchar(15) NOT NULL default '0',datatransfer int(10) unsigned default '0',executiontime mediumint(8) unsigned default '0',PRIMARY KEY  (date,ipaddress)) ENGINE=MyISAM;
DROP TABLE IF EXISTS net2ftp_users;
CREATE TABLE net2ftp_users (ftpserver varchar(255) NOT NULL default '0',username text NOT NULL,homedirectory text NOT NULL,KEY index1 (ftpserver,username(50))) ENGINE=MyISAM;




CREATE TABLE IF NOT EXISTS `vps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reseller` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `panelusername` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `status` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `vpsname` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `description` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `hostip` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `ip` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `ip2` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `netmask` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `broadcast` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `gateway` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci NOT NULL,
  `image_template` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `cdimage` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `ram` int(11) DEFAULT NULL,
  `cpu` int(11) DEFAULT NULL,
  `state` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `ping` varchar(10) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT NULL,
  `hdimage` varchar(200) DEFAULT NULL,
  `vncpassword` varchar(20) DEFAULT NULL,
  `addvpscmd` text default null,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='ehcp db - list of domains and their properties';


CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(20) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
  `reseller` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
  `panelusername` varchar(30) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
  `name` varchar(40) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
  `value` text CHARACTER SET utf8 COLLATE utf8_turkish_ci,
  `longvalue` text CHARACTER SET utf8 COLLATE utf8_turkish_ci,
  `comment` varchar(100) CHARACTER SET utf8 COLLATE utf8_turkish_ci DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='ehcp db - Table for misc configruation of ehcp';
