<?php
###############################
## ResourceSpace
## Local Configuration Script
###############################

require 'config.sensitive.php'; //sensitive private data: urls, passwords, keys

# All custom settings should be entered in this file.
# Options may be copied from config.default.php and configured here.

$mysql_bin_path = '/usr/bin';

# Base URL of the installation
$baseurl = 'http://gallery.midburn.org';

# Email settings
$email_from = 'it@midburn.org';
#$email_from = 'resourcespace@midburn.org';
$email_notify = 'it@midburn.org';
#$email_notify = 'resourcespace@midburn.org';


# Use an external SMTP server for outgoing emails (e.g. Gmail).
$use_smtp=true;
#enable php mailer - this is also to allow HTML format emails.
$use_phpmailer=true;


# Paths
$imagemagick_path = '/usr/bin';
$ghostscript_path = '/usr/bin';
$ffmpeg_path = '/usr/bin';
$exiftool_path = '/usr/bin';
$antiword_path = '/usr/bin';
$pdftotext_path = '/usr/bin';


#Design Changes
$slimheader=true;

$research_request=false; //also stored in the database

# Add the collections footer
$collections_footer = true;

# Enable collection commenting and ranking
$collection_commenting = true;


# Use the JQuery UI Widget instead of the Queue interface (includes a stop button and optional thumbnail mode
$plupload_widget=true;
$plupload_widget_thumbnails=true;


# Set $collection_download to true to enable download of collections as archives (e.g. zip files).
$collection_download = true;

$collection_download_settings[0]["name"] = 'ZIP';
$collection_download_settings[0]["extension"] = 'zip';
$collection_download_settings[0]["arguments"] = '-j';
$collection_download_settings[0]["mime"] = 'application/zip';

# Path to an archiver utility - uncomment and set the lines below if download of collections is enabled ($collection_download = true)
$archiver_path = '/usr/bin';
$archiver_executable = 'zip';
$archiver_listfile_argument = "-@ <";
$use_zip_extension=true; //use php-zip extension instead of $archiver or $zipcommand



$enable_add_collection_on_upload=true;
$upload_collection_name_required=true;

# Show an upload link in the top navigation? (if 't' and 'c' permissions for the current user)
$top_nav_upload=true;
# Show an upload link in the top navigation in addition to 'my contributions' for standard user? (if 'd' permission for the current user)
$top_nav_upload_user=true;
$top_nav_upload_type="plupload"; # The upload type. Options are plupload, ftp, local


#$allow_resource_deletion = true;
# Resource deletion state
# When resources are deleted, the variable below can be set to move the resources into an alternative state instead of removing the resource and its files from the system entirely.
# 
# The resource will still be removed from any collections it has been added to.
#
# Possible options are:
#
# -2	User Contributed Pending Review (not useful unless deleting user-contributed resources)
# -1	User Contributed Pending Submission (not useful unless deleting user-contributed resources) 
# 1		Waiting to be archived
# 2 	Archived
# 3		Deleted (recommended)
$resource_deletion_state=3;
#$delete_requires_password=true;

/*

New Installation Defaults
-------------------------

The following configuration options are set for new installations only.
This provides a mechanism for enabling new features for new installations without affecting existing installations (as would occur with changes to config.default.php)

*/
                                
$thumbs_display_fields = array(8,3);
$list_display_fields = array(8,3,12);
$sort_fields = array(12);

// Set imagemagick default for new installs to expect the newer version with the sRGB bug fixed.
$imagemagick_colorspace = "sRGB";

$slideshow_big=true;
$home_slideshow_width=1400;
$home_slideshow_height=900;

$homeanim_folder="gfx/homeanim";




# User account application - auto creation
# By default this is switched off and applications for new user accounts will be sent as e-mails
# Enabling this option means user accounts will be created but will need to be approved by an administrator
# before the user can log in.
$user_account_auto_creation=true;
#$user_account_auto_creation_usergroup=2; # which user group for auto-created accounts? (see also $registration_group_select - allows users to select the group themselves).

$enable_copy_data_from=false;
$plugins = array('transform', 'rse_version', 'lightbox_preview', 'rse_search_notifications');

$show_status_and_access_on_upload=false;
$show_status_and_access_on_upload_perm=false;
$show_access_on_upload=false;

# New mode that means the upload goes first, then the users edit and approve resources moving them to the correct stage.
$upload_then_edit=false;

# 3-stage upload process: 1. set common fields (collection, date..); 2. batch upload; 3. set per-resource fields (keywords, name...). valid when $upload_then_edit=false
$global_edit_then_upload_then_edit = true;

# limit the height of the preview image when uploading. set to 0 for unlimited
$max_image_preview_height = 250;

$edit_show_save_clear_buttons_at_top = false;

$disable_auto_next = true;

$show_required_field_label = false;

# Option to allow users to 'lock' metadata fields in upload_then_edit_mode.
$upload_review_lock_metadata = true;

# Basic option to visually hide resource types when searching and uploading
# Note: these resource types will still be available (subject to filtering)
$hide_resource_types = array(2,3,4);

# hide the "file" textual label in the upload form
$hide_file_label_on_upload = true;

# Hide Welcome Text
$no_welcometext = true;

# On some PHP installations, the imagerotate() function is wrong and images are rotated in the opposite direction
# to that specified in the dropdown on the edit page.
# Set this option to 'true' to rectify this.
$image_rotate_reverse_options=true;


# When batch uploading, show the 'add resources to collection' selection box
$enable_add_collection_on_upload=true;

$use_checkboxes_for_selection=true;

$show_next_button_on_edit = false;

$fancy_edit_nav_button = true;

