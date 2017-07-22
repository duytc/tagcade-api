-- truncate all performance report tables
set foreign_key_checks = 0;

truncate table report_performance_display_hierarchy_platform;
truncate table report_performance_display_hierarchy_platform_account;
truncate table report_performance_display_hierarchy_platform_site;
truncate table report_performance_display_hierarchy_platform_ad_slot;
truncate table report_performance_display_hierarchy_platform_ad_tag;

truncate table report_performance_display_hierarchy_ad_network;
truncate table report_performance_display_hierarchy_ad_network_site;
truncate table report_performance_display_hierarchy_ad_network_ad_tag;

truncate table report_performance_display_hierarchy_segment_segment;
truncate table report_performance_display_hierarchy_segment_ron_ad_slot;
truncate table report_performance_display_hierarchy_segment_ron_ad_tag;

TRUNCATE table report_performance_display_hierarchy_sub_publisher;
TRUNCATE table report_performance_display_hierarchy_sub_publisher_ad_network;


-- truncate all source report tables
set foreign_key_checks = 0;

truncate table report_source_report;
truncate table report_source_report_record;
truncate table report_source_report_record_x_tracking_key;
truncate table report_source_tracking_key;
truncate table report_source_tracking_term;


-- drop all performance report tables
--
-- set foreign_key_checks = 0;
-- drop table report_performance_display_platform;
-- drop table report_performance_display_account;
-- drop table report_performance_display_site;
-- drop table report_performance_display_ad_slot;
-- drop table report_performance_display_ad_tag;
--
-- drop table report_performance_display_hierarchy_ad_network;
-- drop table report_performance_display_hierarchy_ad_network_site;
-- drop table report_performance_display_hierarchy_ad_network_ad_tag;
--
-- drop table report_performance_display_hierarchy_segment_segment;
-- drop table report_performance_display_hierarchy_segment_ron_ad_slot;
-- drop table report_performance_display_hierarchy_segment_ron_ad_tag;


-- drop all source report tables
--
-- set foreign_key_checks = 0;
-- drop table report_source_report;
-- drop table report_source_report_record;
-- drop table report_source_report_record_x_tracking_key;
-- drop table report_source_tracking_key;
-- drop table report_source_tracking_term;
