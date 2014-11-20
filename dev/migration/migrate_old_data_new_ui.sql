/*
mysqldump -uroot -p tagcade_pub publishers sites ad_networks ad_slots ad_tags ad_sizes > tagcade_temp.sql
*/

set foreign_key_checks = 0;

truncate table tagcade_api.core_ad_network;
truncate table tagcade_api.core_ad_slot;
truncate table tagcade_api.core_ad_tag;
truncate table tagcade_api.core_site;
truncate table tagcade_api.core_user;

insert into tagcade_api.core_user
  select * from tagcade_temp.publishers;

insert into tagcade_api.core_site (id, publisher_id, name, domain)
  select id, publisher_id, name, url from tagcade_temp.sites;

insert into tagcade_api.core_ad_network (id, publisher_id, name, url)
  select id, publisher_id, name, url from tagcade_temp.ad_networks;

insert into tagcade_api.core_ad_slot (id, site_id, name, width, height)
  select sl.id, sl.site_id, sl.name, sz.width, sz.height from tagcade_temp.ad_slots sl
  left join tagcade_temp.ad_sizes sz on sz.id = sl.ad_size_id
  where site_id in (select id from tagcade_api.core_site);

insert into core_ad_tag (id, ad_slot_id, ad_network_id, name, html, position)
  select id, ad_slot_id, ad_network_id, name, tag as html, position from tagcade_temp.ad_tags;

/*
reports
 */
 
 CREATE TABLE IF NOT EXISTS reports_ad_slots (
  date date NOT NULL,
  ad_slot_id int(11) NULL,
  slot_opportunities int(11) NOT NULL,
  KEY date (date,ad_slot_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS reports_ad_tags (
  date date NOT NULL,
  ad_tag_id int(11) NULL,
  opportunities int(11) NOT NULL,
  impressions int(11) NOT NULL,
  fallback_impressions int(11) NOT NULL,
  KEY date (date,ad_tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

insert into tagcade_temp.reports_ad_tags (select r.date, r.ad_tag_id, r.opportunities, r.impressions, r.fallback_impressions from tagcade_pub.reports_ad_tags r where r.date >= '2014-10-01');
insert into tagcade_temp.reports_ad_slots (select r.date, r.ad_slot_id, r.opportunities from tagcade_pub.reports_ad_slots r where r.date >= '2014-10-01');

/* source reports */

set foreign_key_checks = 0;

insert into tagcade_api.report_source_report
  select * from tagcade_temp.source_reports;

insert into tagcade_api.report_source_report_record
  select * from tagcade_temp.source_report_records;

insert into tagcade_api.report_source_report_record_x_tracking_key
  select * from tagcade_temp.source_report_record_tracking_keys;

insert into tagcade_api.report_source_tracking_key
  select * from tagcade_temp.tracking_keys;

insert into tagcade_api.report_source_tracking_term
  select * from tagcade_temp.tracking_terms;