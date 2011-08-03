


CREATE TABLE AreaGroupBlockTypes (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  arHandle varchar(255) NOT NULL,
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  btID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cID,arHandle,gID,uID,btID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AreaGroups (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  arHandle varchar(255) NOT NULL,
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  agPermissions varchar(64) NOT NULL,
  PRIMARY KEY (cID,arHandle,gID,uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Areas (
  arID int(10) unsigned NOT NULL AUTO_INCREMENT,
  cID int(10) unsigned NOT NULL DEFAULT '0',
  arHandle varchar(255) NOT NULL,
  arOverrideCollectionPermissions tinyint(1) NOT NULL DEFAULT '0',
  arInheritPermissionsFromAreaOnCID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (arID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atAddress (
  avID int(10) unsigned NOT NULL DEFAULT '0',
  address1 varchar(255) DEFAULT NULL,
  address2 varchar(255) DEFAULT NULL,
  city varchar(255) DEFAULT NULL,
  state_province varchar(255) DEFAULT NULL,
  country varchar(4) DEFAULT NULL,
  postal_code varchar(32) DEFAULT NULL,
  PRIMARY KEY (avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atAddressCustomCountries (
  atAddressCustomCountryID int(10) unsigned NOT NULL AUTO_INCREMENT,
  akID int(10) unsigned NOT NULL DEFAULT '0',
  country varchar(5) NOT NULL,
  PRIMARY KEY (atAddressCustomCountryID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atAddressSettings (
  akID int(10) unsigned NOT NULL DEFAULT '0',
  akHasCustomCountries int(1) NOT NULL DEFAULT '0',
  akDefaultCountry varchar(12) DEFAULT NULL,
  PRIMARY KEY (akID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atAttributeKeyCategoryItemsSettings (
  akID int(11) NOT NULL DEFAULT '0',
  akCategoryHandle varchar(255) DEFAULT NULL,
  PRIMARY KEY (akID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atBoolean (
  avID int(10) unsigned NOT NULL,
  `value` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atBooleanSettings (
  akID int(10) unsigned NOT NULL,
  akCheckedByDefault tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (akID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atDateTime (
  avID int(10) unsigned NOT NULL,
  `value` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atDateTimeSettings (
  akID int(10) unsigned NOT NULL,
  akDateDisplayMode varchar(255) DEFAULT NULL,
  PRIMARY KEY (akID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atDefault (
  avID int(10) unsigned NOT NULL,
  `value` longtext,
  PRIMARY KEY (avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atFile (
  avID int(10) unsigned NOT NULL,
  fID int(10) unsigned NOT NULL,
  PRIMARY KEY (avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atNumber (
  avID int(10) unsigned NOT NULL,
  `value` decimal(14,4) DEFAULT '0.0000',
  PRIMARY KEY (avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atSelectOptions (
  ID int(10) unsigned NOT NULL AUTO_INCREMENT,
  akID int(10) unsigned DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  displayOrder int(10) unsigned DEFAULT NULL,
  isEndUserAdded tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atSelectOptionsSelected (
  avID int(10) unsigned NOT NULL,
  atSelectOptionID int(10) unsigned NOT NULL,
  PRIMARY KEY (avID,atSelectOptionID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atSelectSettings (
  akID int(10) unsigned NOT NULL,
  akSelectAllowMultipleValues tinyint(1) NOT NULL DEFAULT '0',
  akSelectOptionDisplayOrder varchar(255) NOT NULL DEFAULT 'display_asc',
  akSelectAllowOtherValues tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (akID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE atTextareaSettings (
  akID int(10) unsigned NOT NULL DEFAULT '0',
  akTextareaDisplayMode varchar(255) DEFAULT NULL,
  PRIMARY KEY (akID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeKeyCategories (
  akCategoryID int(10) unsigned NOT NULL AUTO_INCREMENT,
  akCategoryHandle varchar(255) NOT NULL,
  akCategoryAllowSets smallint(4) NOT NULL DEFAULT '0',
  pkgID int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (akCategoryID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeKeyCategoryItemAttributeValues (
  ID int(10) unsigned NOT NULL,
  akID int(10) unsigned NOT NULL,
  avID int(10) unsigned NOT NULL,
  akCategoryHandle varchar(255) NOT NULL,
  PRIMARY KEY (ID,akID,avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeKeyCategoryItemPermissions (
  ID varchar(255) NOT NULL,
  akCategoryHandle varchar(255) DEFAULT NULL,
  gID int(10) DEFAULT NULL,
  uID int(10) DEFAULT NULL,
  canRead tinyint(1) DEFAULT '0',
  canWrite tinyint(1) DEFAULT '0',
  canDelete tinyint(1) DEFAULT '0',
  canAdd tinyint(1) DEFAULT '0',
  canSearch tinyint(1) DEFAULT '0',
  canAdmin tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeKeyCategoryItems (
  ID int(10) unsigned NOT NULL AUTO_INCREMENT,
  akCategoryHandle varchar(255) NOT NULL,
  uID int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeKeyCategoryItemSearchIndex (
  ID int(10) unsigned NOT NULL,
  PRIMARY KEY (ID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeKeys (
  akID int(10) unsigned NOT NULL AUTO_INCREMENT,
  akHandle varchar(255) NOT NULL,
  akName varchar(255) NOT NULL,
  akIsSearchable tinyint(1) NOT NULL DEFAULT '0',
  akIsSearchableIndexed tinyint(1) NOT NULL DEFAULT '0',
  akIsAutoCreated tinyint(1) NOT NULL DEFAULT '0',
  akIsColumnHeader tinyint(1) NOT NULL DEFAULT '0',
  akIsEditable tinyint(1) NOT NULL DEFAULT '0',
  atID int(10) unsigned DEFAULT NULL,
  akCategoryID int(10) unsigned DEFAULT NULL,
  pkgID int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (akID),
  UNIQUE KEY akHandle (akHandle,akCategoryID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeSetKeys (
  akID int(10) unsigned NOT NULL DEFAULT '0',
  asID int(10) unsigned NOT NULL DEFAULT '0',
  displayOrder int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (akID,asID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeSets (
  asID int(10) unsigned NOT NULL AUTO_INCREMENT,
  asName varchar(255) DEFAULT NULL,
  asHandle varchar(255) NOT NULL,
  akCategoryID int(10) unsigned NOT NULL DEFAULT '0',
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (asID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeTypeCategories (
  atID int(10) unsigned NOT NULL DEFAULT '0',
  akCategoryID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (atID,akCategoryID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeTypes (
  atID int(10) unsigned NOT NULL AUTO_INCREMENT,
  atHandle varchar(255) NOT NULL,
  atName varchar(255) NOT NULL,
  pkgID int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (atID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE AttributeValues (
  avID int(10) unsigned NOT NULL AUTO_INCREMENT,
  akID int(10) unsigned DEFAULT NULL,
  avDateAdded datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  uID int(10) unsigned DEFAULT NULL,
  atID int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE BlockRelations (
  brID int(10) unsigned NOT NULL AUTO_INCREMENT,
  bID int(10) unsigned NOT NULL DEFAULT '0',
  originalBID int(10) unsigned NOT NULL DEFAULT '0',
  relationType varchar(50) NOT NULL,
  PRIMARY KEY (brID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Blocks (
  bID int(10) unsigned NOT NULL AUTO_INCREMENT,
  bName varchar(60) DEFAULT NULL,
  bDateAdded datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  bDateModified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  bFilename varchar(32) DEFAULT NULL,
  bIsActive varchar(1) NOT NULL DEFAULT '1',
  btID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE BlockTypes (
  btID int(10) unsigned NOT NULL AUTO_INCREMENT,
  btHandle varchar(32) NOT NULL,
  btName varchar(128) NOT NULL,
  btDescription text,
  btActiveWhenAdded tinyint(1) NOT NULL DEFAULT '1',
  btCopyWhenPropagate tinyint(1) NOT NULL DEFAULT '0',
  btIncludeAll tinyint(1) NOT NULL DEFAULT '0',
  btIsInternal tinyint(1) NOT NULL DEFAULT '0',
  btInterfaceWidth int(10) unsigned NOT NULL DEFAULT '400',
  btInterfaceHeight int(10) unsigned NOT NULL DEFAULT '400',
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (btID),
  UNIQUE KEY btHandle (btHandle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btContentFile (
  bID int(10) unsigned NOT NULL,
  fID int(10) unsigned DEFAULT NULL,
  fileLinkText varchar(255) DEFAULT NULL,
  filePassword varchar(255) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btContentImage (
  bID int(10) unsigned NOT NULL,
  fID int(10) unsigned DEFAULT '0',
  fOnstateID int(10) unsigned DEFAULT '0',
  maxWidth int(10) unsigned DEFAULT '0',
  maxHeight int(10) unsigned DEFAULT '0',
  externalLink varchar(255) DEFAULT NULL,
  altText varchar(255) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btContentLocal (
  bID int(10) unsigned NOT NULL,
  content longtext,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btDateNav (
  bID int(10) unsigned NOT NULL,
  num smallint(5) unsigned NOT NULL,
  cParentID int(10) unsigned NOT NULL DEFAULT '1',
  cThis tinyint(3) unsigned NOT NULL DEFAULT '0',
  ctID smallint(5) unsigned DEFAULT NULL,
  flatDisplay int(11) DEFAULT '0',
  defaultNode varchar(64) DEFAULT 'current_page',
  truncateTitles int(11) DEFAULT '0',
  truncateSummaries int(11) DEFAULT '0',
  displayFeaturedOnly int(11) DEFAULT '0',
  truncateChars int(11) DEFAULT '128',
  truncateTitleChars int(11) DEFAULT '128',
  showDescriptions int(11) DEFAULT '0',
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btExternalForm (
  bID int(10) unsigned NOT NULL,
  filename varchar(128) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btFile (
  bID int(10) unsigned NOT NULL,
  filename varchar(255) DEFAULT NULL,
  origfilename varchar(255) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL,
  generictype varchar(32) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btFlashContent (
  bID int(10) unsigned NOT NULL,
  fID int(10) unsigned DEFAULT NULL,
  quality varchar(255) DEFAULT NULL,
  minVersion varchar(255) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btForm (
  bID int(10) unsigned NOT NULL,
  questionSetId int(10) unsigned DEFAULT '0',
  surveyName varchar(255) DEFAULT NULL,
  thankyouMsg text,
  notifyMeOnSubmission tinyint(3) unsigned NOT NULL DEFAULT '0',
  recipientEmail varchar(255) DEFAULT NULL,
  displayCaptcha int(11) DEFAULT '1',
  redirectCID int(11) DEFAULT '0',
  PRIMARY KEY (bID),
  KEY questionSetIdForeign (questionSetId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btFormAnswers (
  aID int(10) unsigned NOT NULL AUTO_INCREMENT,
  asID int(10) unsigned DEFAULT '0',
  msqID int(10) unsigned DEFAULT '0',
  answer varchar(255) DEFAULT NULL,
  answerLong text,
  PRIMARY KEY (aID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btFormAnswerSet (
  asID int(10) unsigned NOT NULL AUTO_INCREMENT,
  questionSetId int(10) unsigned DEFAULT '0',
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  uID int(10) unsigned DEFAULT '0',
  PRIMARY KEY (asID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btFormQuestions (
  qID int(10) unsigned NOT NULL AUTO_INCREMENT,
  msqID int(10) unsigned DEFAULT '0',
  bID int(10) unsigned DEFAULT '0',
  questionSetId int(10) unsigned DEFAULT '0',
  question varchar(255) DEFAULT NULL,
  inputType varchar(255) DEFAULT NULL,
  `options` text,
  position int(10) unsigned DEFAULT '1000',
  width int(10) unsigned DEFAULT '50',
  height int(10) unsigned DEFAULT '3',
  required int(11) DEFAULT '0',
  PRIMARY KEY (qID),
  KEY questionSetId (questionSetId),
  KEY msqID (msqID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btGoogleMap (
  bID int(10) unsigned NOT NULL,
  title varchar(255) DEFAULT NULL,
  location varchar(255) DEFAULT NULL,
  latitude double DEFAULT NULL,
  longitude double DEFAULT NULL,
  zoom int(8) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btGuestBook (
  bID int(10) unsigned NOT NULL,
  requireApproval int(11) DEFAULT '0',
  title varchar(100) DEFAULT 'Comments',
  dateFormat varchar(100) DEFAULT NULL,
  displayGuestBookForm int(11) DEFAULT '1',
  displayCaptcha int(11) DEFAULT '1',
  authenticationRequired int(11) DEFAULT '0',
  notifyEmail varchar(100) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btGuestBookEntries (
  bID int(11) DEFAULT NULL,
  cID int(11) DEFAULT '1',
  entryID int(11) NOT NULL AUTO_INCREMENT,
  uID int(11) DEFAULT '0',
  commentText longtext,
  user_name varchar(100) DEFAULT NULL,
  user_email varchar(100) DEFAULT NULL,
  entryDate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  approved int(11) DEFAULT '1',
  PRIMARY KEY (entryID),
  KEY cID (cID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btNavigation (
  bID int(10) unsigned NOT NULL,
  orderBy varchar(255) DEFAULT 'alpha_asc',
  displayPages varchar(255) DEFAULT 'top',
  displayPagesCID int(10) unsigned NOT NULL DEFAULT '1',
  displayPagesIncludeSelf tinyint(3) unsigned NOT NULL DEFAULT '0',
  displaySubPages varchar(255) DEFAULT 'none',
  displaySubPageLevels varchar(255) DEFAULT 'none',
  displaySubPageLevelsNum smallint(5) unsigned NOT NULL DEFAULT '0',
  displayUnavailablePages tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btNextPrevious (
  bID int(10) unsigned NOT NULL,
  linkStyle varchar(32) DEFAULT NULL,
  nextLabel varchar(128) DEFAULT NULL,
  previousLabel varchar(128) DEFAULT NULL,
  parentLabel varchar(128) DEFAULT NULL,
  showArrows int(11) DEFAULT '1',
  loopSequence int(11) DEFAULT '1',
  excludeSystemPages int(11) DEFAULT '1',
  orderBy varchar(20) DEFAULT 'display_asc',
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btPageList (
  bID int(10) unsigned NOT NULL,
  num smallint(5) unsigned NOT NULL,
  orderBy varchar(32) DEFAULT NULL,
  cParentID int(10) unsigned NOT NULL DEFAULT '1',
  cThis tinyint(3) unsigned NOT NULL DEFAULT '0',
  paginate tinyint(3) unsigned NOT NULL DEFAULT '0',
  displayAliases tinyint(3) unsigned NOT NULL DEFAULT '1',
  ctID smallint(5) unsigned DEFAULT NULL,
  rss int(11) DEFAULT '0',
  rssTitle varchar(255) DEFAULT NULL,
  rssDescription longtext,
  truncateSummaries int(11) DEFAULT '0',
  displayFeaturedOnly int(11) DEFAULT '0',
  truncateChars int(11) DEFAULT '128',
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btRssDisplay (
  bID int(10) unsigned NOT NULL,
  title varchar(255) DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  dateFormat varchar(100) DEFAULT NULL,
  itemsToDisplay int(10) unsigned DEFAULT '5',
  showSummary tinyint(3) unsigned NOT NULL DEFAULT '1',
  launchInNewWindow tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btSearch (
  bID int(10) unsigned NOT NULL,
  title varchar(255) DEFAULT NULL,
  buttonText varchar(128) DEFAULT NULL,
  baseSearchPath varchar(255) DEFAULT NULL,
  resultsURL varchar(255) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btSlideshow (
  bID int(10) unsigned NOT NULL,
  fsID int(10) unsigned DEFAULT NULL,
  playback varchar(50) DEFAULT NULL,
  duration int(10) unsigned DEFAULT NULL,
  fadeDuration int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btSlideshowImg (
  slideshowImgId int(10) unsigned NOT NULL AUTO_INCREMENT,
  bID int(10) unsigned DEFAULT NULL,
  fID int(10) unsigned DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  duration int(10) unsigned DEFAULT NULL,
  fadeDuration int(10) unsigned DEFAULT NULL,
  groupSet int(10) unsigned DEFAULT NULL,
  position int(10) unsigned DEFAULT NULL,
  imgHeight int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (slideshowImgId)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btSurvey (
  bID int(10) unsigned NOT NULL,
  question varchar(255) DEFAULT '',
  requiresRegistration int(11) DEFAULT '0',
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btSurveyOptions (
  optionID int(10) unsigned NOT NULL AUTO_INCREMENT,
  bID int(11) DEFAULT NULL,
  optionName varchar(255) DEFAULT NULL,
  displayOrder int(11) DEFAULT '0',
  PRIMARY KEY (optionID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btSurveyResults (
  resultID int(10) unsigned NOT NULL AUTO_INCREMENT,
  optionID int(10) unsigned DEFAULT '0',
  uID int(10) unsigned DEFAULT '0',
  bID int(11) DEFAULT NULL,
  cID int(11) DEFAULT NULL,
  ipAddress varchar(128) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (resultID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btTags (
  bID int(10) unsigned NOT NULL,
  title varchar(255) DEFAULT NULL,
  targetCID int(11) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btVideo (
  bID int(10) unsigned NOT NULL,
  fID int(10) unsigned DEFAULT NULL,
  width int(10) unsigned DEFAULT NULL,
  height int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE btYouTube (
  bID int(10) unsigned NOT NULL,
  title varchar(255) DEFAULT NULL,
  videoURL varchar(255) DEFAULT NULL,
  PRIMARY KEY (bID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CollectionAttributeValues (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  cvID int(10) unsigned NOT NULL DEFAULT '0',
  akID int(10) unsigned NOT NULL DEFAULT '0',
  avID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cID,cvID,akID,avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Collections (
  cID int(10) unsigned NOT NULL AUTO_INCREMENT,
  cDateAdded datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  cDateModified datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  cHandle varchar(255) DEFAULT NULL,
  PRIMARY KEY (cID),
  KEY cDateModified (cDateModified),
  KEY cDateAdded (cDateAdded)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CollectionSearchIndexAttributes (
  cID int(11) unsigned NOT NULL DEFAULT '0',
  ak_meta_title text,
  ak_meta_description text,
  ak_meta_keywords text,
  ak_exclude_nav tinyint(4) DEFAULT '0',
  ak_exclude_page_list tinyint(4) DEFAULT '0',
  ak_header_extra_content text,
  ak_exclude_search_index tinyint(4) DEFAULT '0',
  ak_exclude_sitemapxml tinyint(4) DEFAULT '0',
  ak_tags text,
  PRIMARY KEY (cID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CollectionVersionAreaLayouts (
  cvalID int(10) unsigned NOT NULL AUTO_INCREMENT,
  cID int(10) unsigned DEFAULT '0',
  cvID int(10) unsigned DEFAULT '0',
  arHandle varchar(255) DEFAULT NULL,
  layoutID int(10) unsigned NOT NULL DEFAULT '0',
  position int(10) DEFAULT '1000',
  areaNameNumber int(10) unsigned DEFAULT '0',
  PRIMARY KEY (cvalID),
  KEY areaLayoutsIndex (cID,cvID,arHandle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CollectionVersionAreaStyles (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  cvID int(10) unsigned NOT NULL DEFAULT '0',
  arHandle varchar(255) NOT NULL,
  csrID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cID,cvID,arHandle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CollectionVersionBlockPermissions (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  cvID int(10) unsigned NOT NULL DEFAULT '1',
  bID int(10) unsigned NOT NULL DEFAULT '0',
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  cbgPermissions varchar(32) DEFAULT NULL,
  PRIMARY KEY (cID,cvID,bID,gID,uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CollectionVersionBlocks (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  cvID int(10) unsigned NOT NULL DEFAULT '1',
  bID int(10) unsigned NOT NULL DEFAULT '0',
  arHandle varchar(255) NOT NULL,
  cbDisplayOrder int(10) unsigned NOT NULL DEFAULT '0',
  isOriginal varchar(1) NOT NULL DEFAULT '0',
  cbOverrideAreaPermissions tinyint(1) NOT NULL DEFAULT '0',
  cbIncludeAll tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (cID,cvID,bID,arHandle),
  KEY cbIncludeAll (cbIncludeAll)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CollectionVersionBlockStyles (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  cvID int(10) unsigned NOT NULL DEFAULT '0',
  bID int(10) unsigned NOT NULL DEFAULT '0',
  arHandle varchar(255) NOT NULL,
  csrID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cID,cvID,bID,arHandle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CollectionVersions (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  cvID int(10) unsigned NOT NULL DEFAULT '1',
  cvName text,
  cvHandle varchar(64) DEFAULT NULL,
  cvDescription text,
  cvDatePublic datetime DEFAULT NULL,
  cvDateCreated datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  cvComments varchar(255) DEFAULT NULL,
  cvIsApproved tinyint(1) NOT NULL DEFAULT '0',
  cvIsNew tinyint(1) NOT NULL DEFAULT '0',
  cvAuthorUID int(10) unsigned DEFAULT NULL,
  cvApproverUID int(10) unsigned DEFAULT NULL,
  cvActivateDatetime datetime DEFAULT NULL,
  PRIMARY KEY (cID,cvID),
  KEY cvIsApproved (cvIsApproved)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE ComposerContentLayout (
  cclID int(10) unsigned NOT NULL AUTO_INCREMENT,
  bID int(10) unsigned NOT NULL DEFAULT '0',
  akID int(10) unsigned NOT NULL DEFAULT '0',
  displayOrder int(10) unsigned NOT NULL DEFAULT '0',
  ctID int(10) unsigned NOT NULL DEFAULT '0',
  ccFilename varchar(128) DEFAULT NULL,
  PRIMARY KEY (cclID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE ComposerDrafts (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  cpPublishParentID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE ComposerTypes (
  ctID int(10) unsigned NOT NULL DEFAULT '0',
  ctComposerPublishPageMethod varchar(64) NOT NULL DEFAULT 'CHOOSE',
  ctComposerPublishPageTypeID int(10) unsigned NOT NULL DEFAULT '0',
  ctComposerPublishPageParentID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ctID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Config (
  cfKey varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  cfValue longtext,
  uID int(10) unsigned NOT NULL DEFAULT '0',
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cfKey,uID),
  KEY uID (uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CustomStylePresets (
  cspID int(10) unsigned NOT NULL AUTO_INCREMENT,
  cspName varchar(255) NOT NULL,
  csrID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cspID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE CustomStyleRules (
  csrID int(10) unsigned NOT NULL AUTO_INCREMENT,
  css_id varchar(128) DEFAULT NULL,
  css_class varchar(128) DEFAULT NULL,
  css_serialized text,
  css_custom text,
  PRIMARY KEY (csrID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE DashboardHomepage (
  dbhID int(10) unsigned NOT NULL AUTO_INCREMENT,
  dbhModule varchar(255) NOT NULL,
  dbhDisplayName varchar(255) DEFAULT NULL,
  dbhDisplayOrder int(10) unsigned NOT NULL DEFAULT '0',
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (dbhID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE DownloadStatistics (
  dsID int(10) unsigned NOT NULL AUTO_INCREMENT,
  fID int(10) unsigned NOT NULL,
  fvID int(10) unsigned NOT NULL,
  uID int(10) unsigned NOT NULL,
  rcID int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (dsID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileAttributeValues (
  fID int(10) unsigned NOT NULL DEFAULT '0',
  fvID int(10) unsigned NOT NULL DEFAULT '0',
  akID int(10) unsigned NOT NULL DEFAULT '0',
  avID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (fID,fvID,akID,avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FilePermissionFileTypes (
  fsID int(10) unsigned NOT NULL DEFAULT '0',
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  extension varchar(32) NOT NULL,
  PRIMARY KEY (fsID,gID,uID,extension)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FilePermissions (
  fID int(10) unsigned NOT NULL DEFAULT '0',
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  canRead int(4) NOT NULL DEFAULT '0',
  canWrite int(4) NOT NULL DEFAULT '0',
  canAdmin int(4) NOT NULL DEFAULT '0',
  canSearch int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (fID,gID,uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Files (
  fID int(10) unsigned NOT NULL AUTO_INCREMENT,
  fDateAdded datetime DEFAULT NULL,
  uID int(10) unsigned NOT NULL DEFAULT '0',
  fslID int(10) unsigned NOT NULL DEFAULT '0',
  ocID int(10) unsigned NOT NULL DEFAULT '0',
  fOverrideSetPermissions int(1) NOT NULL DEFAULT '0',
  fPassword varchar(255) DEFAULT NULL,
  PRIMARY KEY (fID,uID,fslID),
  KEY fOverrideSetPermissions (fOverrideSetPermissions)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileSearchIndexAttributes (
  fID int(11) unsigned NOT NULL DEFAULT '0',
  ak_width decimal(14,4) DEFAULT '0.0000',
  ak_height decimal(14,4) DEFAULT '0.0000',
  PRIMARY KEY (fID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileSetFiles (
  fsfID int(10) unsigned NOT NULL AUTO_INCREMENT,
  fID int(10) unsigned NOT NULL,
  fsID int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  fsDisplayOrder int(10) unsigned NOT NULL,
  PRIMARY KEY (fsfID),
  KEY fID (fID),
  KEY fsID (fsID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileSetPermissions (
  fsID int(10) unsigned NOT NULL DEFAULT '0',
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  canRead int(4) DEFAULT NULL,
  canWrite int(4) DEFAULT NULL,
  canAdmin int(4) DEFAULT NULL,
  canAdd int(4) DEFAULT NULL,
  canSearch int(3) unsigned DEFAULT NULL,
  PRIMARY KEY (fsID,gID,uID),
  KEY canRead (canRead),
  KEY canWrite (canWrite),
  KEY canAdmin (canAdmin),
  KEY canSearch (canSearch),
  KEY canAdd (canAdd)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileSets (
  fsID int(10) unsigned NOT NULL AUTO_INCREMENT,
  fsName varchar(64) NOT NULL,
  uID int(10) unsigned NOT NULL DEFAULT '0',
  fsType int(4) NOT NULL,
  fsOverrideGlobalPermissions int(4) DEFAULT NULL,
  PRIMARY KEY (fsID),
  KEY fsOverrideGlobalPermissions (fsOverrideGlobalPermissions)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileSetSavedSearches (
  fsID int(10) unsigned NOT NULL DEFAULT '0',
  fsSearchRequest text,
  fsResultColumns text,
  PRIMARY KEY (fsID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileStorageLocations (
  fslID int(10) unsigned NOT NULL DEFAULT '0',
  fslName varchar(255) NOT NULL,
  fslDirectory varchar(255) NOT NULL,
  PRIMARY KEY (fslID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileVersionLog (
  fvlID int(10) unsigned NOT NULL AUTO_INCREMENT,
  fID int(10) unsigned NOT NULL DEFAULT '0',
  fvID int(10) unsigned NOT NULL DEFAULT '0',
  fvUpdateTypeID int(3) unsigned NOT NULL DEFAULT '0',
  fvUpdateTypeAttributeID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (fvlID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE FileVersions (
  fID int(10) unsigned NOT NULL DEFAULT '0',
  fvID int(10) unsigned NOT NULL DEFAULT '0',
  fvFilename varchar(255) NOT NULL,
  fvPrefix varchar(12) DEFAULT NULL,
  fvGenericType int(3) unsigned NOT NULL DEFAULT '0',
  fvSize int(20) unsigned NOT NULL DEFAULT '0',
  fvTitle varchar(255) DEFAULT NULL,
  fvDescription text,
  fvTags varchar(255) DEFAULT NULL,
  fvIsApproved int(10) unsigned NOT NULL DEFAULT '1',
  fvDateAdded datetime DEFAULT NULL,
  fvApproverUID int(10) unsigned NOT NULL DEFAULT '0',
  fvAuthorUID int(10) unsigned NOT NULL DEFAULT '0',
  fvActivateDatetime datetime DEFAULT NULL,
  fvHasThumbnail1 int(1) NOT NULL DEFAULT '0',
  fvHasThumbnail2 int(1) NOT NULL DEFAULT '0',
  fvHasThumbnail3 int(1) NOT NULL DEFAULT '0',
  fvExtension varchar(32) DEFAULT NULL,
  fvType int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (fID,fvID),
  KEY fvExtension (fvType),
  KEY fvTitle (fvTitle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Groups (
  gID int(10) unsigned NOT NULL AUTO_INCREMENT,
  gName varchar(128) NOT NULL,
  gDescription varchar(255) NOT NULL,
  gUserExpirationIsEnabled int(1) NOT NULL DEFAULT '0',
  gUserExpirationMethod varchar(12) DEFAULT NULL,
  gUserExpirationSetDateTime datetime DEFAULT NULL,
  gUserExpirationInterval int(10) unsigned NOT NULL DEFAULT '0',
  gUserExpirationAction varchar(20) DEFAULT NULL,
  PRIMARY KEY (gID),
  UNIQUE KEY gName (gName)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Jobs (
  jID int(10) unsigned NOT NULL AUTO_INCREMENT,
  jName varchar(100) NOT NULL,
  jDescription varchar(255) NOT NULL,
  jDateInstalled datetime DEFAULT NULL,
  jDateLastRun datetime DEFAULT NULL,
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  jLastStatusText varchar(255) DEFAULT NULL,
  jLastStatusCode smallint(4) NOT NULL DEFAULT '0',
  jStatus varchar(14) NOT NULL DEFAULT 'ENABLED',
  jHandle varchar(255) NOT NULL,
  jNotUninstallable smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (jID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE JobsLog (
  jlID int(10) unsigned NOT NULL AUTO_INCREMENT,
  jID int(10) unsigned NOT NULL,
  jlMessage varchar(255) NOT NULL,
  jlTimestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  jlError int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (jlID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE LayoutPresets (
  lpID int(10) unsigned NOT NULL AUTO_INCREMENT,
  lpName varchar(128) NOT NULL,
  layoutID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (lpID),
  UNIQUE KEY layoutID (layoutID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Layouts (
  layoutID int(10) unsigned NOT NULL AUTO_INCREMENT,
  layout_rows int(5) NOT NULL DEFAULT '3',
  layout_columns int(3) NOT NULL DEFAULT '3',
  spacing int(3) NOT NULL DEFAULT '3',
  breakpoints varchar(255) NOT NULL DEFAULT '',
  locked tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (layoutID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `Logs` (
  logID int(10) unsigned NOT NULL AUTO_INCREMENT,
  logType varchar(64) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  logText longtext,
  logIsInternal tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (logID),
  KEY logType (logType),
  KEY logIsInternal (logIsInternal)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE MailImporters (
  miID int(10) unsigned NOT NULL AUTO_INCREMENT,
  miHandle varchar(64) NOT NULL,
  miServer varchar(255) DEFAULT NULL,
  miUsername varchar(255) DEFAULT NULL,
  miPassword varchar(255) DEFAULT NULL,
  miEncryption varchar(32) DEFAULT NULL,
  miIsEnabled int(1) NOT NULL DEFAULT '0',
  miEmail varchar(255) DEFAULT NULL,
  miPort int(10) unsigned NOT NULL DEFAULT '0',
  pkgID int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (miID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE MailValidationHashes (
  mvhID int(10) unsigned NOT NULL AUTO_INCREMENT,
  miID int(10) unsigned NOT NULL DEFAULT '0',
  email varchar(255) NOT NULL,
  mHash varchar(128) NOT NULL,
  mDateGenerated int(10) unsigned NOT NULL DEFAULT '0',
  mDateRedeemed int(10) unsigned NOT NULL DEFAULT '0',
  `data` text,
  PRIMARY KEY (mvhID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Packages (
  pkgID int(10) unsigned NOT NULL AUTO_INCREMENT,
  pkgName varchar(255) NOT NULL,
  pkgHandle varchar(64) NOT NULL,
  pkgDescription text,
  pkgDateInstalled datetime NOT NULL,
  pkgIsInstalled tinyint(1) NOT NULL DEFAULT '1',
  pkgVersion varchar(32) DEFAULT NULL,
  pkgAvailableVersion varchar(32) DEFAULT NULL,
  PRIMARY KEY (pkgID),
  UNIQUE KEY pkgHandle (pkgHandle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PagePaths (
  ppID int(10) unsigned NOT NULL AUTO_INCREMENT,
  cID int(10) unsigned DEFAULT '0',
  cPath text,
  ppIsCanonical varchar(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (ppID),
  KEY cID (cID),
  KEY ppIsCanonical (ppIsCanonical)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PagePermissionPageTypes (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  ctID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cID,gID,uID,ctID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PagePermissions (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  cgPermissions varchar(32) DEFAULT NULL,
  cgStartDate datetime DEFAULT NULL,
  cgEndDate datetime DEFAULT NULL,
  PRIMARY KEY (cID,gID,uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Pages (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  ctID int(10) unsigned NOT NULL DEFAULT '0',
  cIsTemplate varchar(1) NOT NULL DEFAULT '0',
  uID int(10) unsigned DEFAULT NULL,
  cIsCheckedOut tinyint(1) NOT NULL DEFAULT '0',
  cCheckedOutUID int(10) unsigned DEFAULT NULL,
  cCheckedOutDatetime datetime DEFAULT NULL,
  cCheckedOutDatetimeLastEdit datetime DEFAULT NULL,
  cPendingAction varchar(6) DEFAULT NULL,
  cPendingActionDatetime datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  cPendingActionUID int(10) unsigned DEFAULT NULL,
  cPendingActionTargetCID int(10) unsigned DEFAULT NULL,
  cOverrideTemplatePermissions tinyint(1) NOT NULL DEFAULT '1',
  cInheritPermissionsFromCID int(10) unsigned NOT NULL DEFAULT '0',
  cInheritPermissionsFrom varchar(8) NOT NULL DEFAULT 'PARENT',
  cFilename varchar(255) DEFAULT NULL,
  cPointerID int(10) unsigned NOT NULL DEFAULT '0',
  cPointerExternalLink varchar(255) DEFAULT NULL,
  cPointerExternalLinkNewWindow tinyint(1) NOT NULL DEFAULT '0',
  cChildren int(10) unsigned NOT NULL DEFAULT '0',
  cDisplayOrder int(10) unsigned NOT NULL DEFAULT '0',
  cParentID int(10) unsigned NOT NULL DEFAULT '0',
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  ptID int(10) unsigned NOT NULL DEFAULT '0',
  cCacheFullPageContent int(4) NOT NULL DEFAULT '-1',
  cCacheFullPageContentOverrideLifetime varchar(32) NOT NULL DEFAULT '0',
  cCacheFullPageContentLifetimeCustom int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (cID),
  KEY cParentID (cParentID),
  KEY cCheckedOutUID (cCheckedOutUID),
  KEY cPointerID (cPointerID),
  KEY uID (uID),
  KEY ctID (ctID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PageSearchIndex (
  cID int(10) unsigned NOT NULL DEFAULT '0',
  content text,
  cName varchar(255) DEFAULT NULL,
  cDescription text,
  cPath text,
  cDatePublic datetime DEFAULT NULL,
  cDateLastIndexed datetime DEFAULT NULL,
  cDateLastSitemapped datetime DEFAULT NULL,
  PRIMARY KEY (cID),
  KEY cDateLastIndexed (cDateLastIndexed),
  KEY cDateLastSitemapped (cDateLastSitemapped),
  FULLTEXT KEY cName (cName),
  FULLTEXT KEY cDescription (cDescription),
  FULLTEXT KEY content (content),
  FULLTEXT KEY content2 (cName,cDescription,content)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PageStatistics (
  pstID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  cID int(10) unsigned NOT NULL DEFAULT '0',
  `date` date DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  uID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (pstID),
  KEY cID (cID),
  KEY `date` (`date`),
  KEY uID (uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PageThemes (
  ptID int(10) unsigned NOT NULL AUTO_INCREMENT,
  ptHandle varchar(64) NOT NULL,
  ptName varchar(255) DEFAULT NULL,
  ptDescription text,
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ptID),
  UNIQUE KEY ptHandle (ptHandle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PageThemeStyles (
  ptID int(10) unsigned NOT NULL DEFAULT '0',
  ptsHandle varchar(128) NOT NULL,
  ptsValue longtext,
  ptsType varchar(32) NOT NULL,
  PRIMARY KEY (ptID,ptsHandle,ptsType)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PageTypeAttributes (
  ctID int(10) unsigned NOT NULL DEFAULT '0',
  akID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ctID,akID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PageTypes (
  ctID int(10) unsigned NOT NULL AUTO_INCREMENT,
  ctHandle varchar(32) NOT NULL,
  ctIcon varchar(128) DEFAULT NULL,
  ctName varchar(90) NOT NULL,
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (ctID),
  UNIQUE KEY ctHandle (ctHandle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE PileContents (
  pcID int(10) unsigned NOT NULL AUTO_INCREMENT,
  pID int(10) unsigned NOT NULL DEFAULT '0',
  itemID int(10) unsigned NOT NULL DEFAULT '0',
  itemType varchar(64) NOT NULL,
  quantity int(10) unsigned NOT NULL DEFAULT '1',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  displayOrder int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (pcID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Piles (
  pID int(10) unsigned NOT NULL AUTO_INCREMENT,
  uID int(10) unsigned DEFAULT NULL,
  isDefault tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `name` varchar(255) DEFAULT NULL,
  state varchar(64) NOT NULL,
  PRIMARY KEY (pID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE SignupRequests (
  id int(11) NOT NULL AUTO_INCREMENT,
  ipFrom int(10) unsigned NOT NULL DEFAULT '0',
  date_access timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY index_ipFrom (ipFrom)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE SystemNotifications (
  snID int(10) unsigned NOT NULL AUTO_INCREMENT,
  snTypeID int(3) unsigned NOT NULL DEFAULT '0',
  snURL text,
  snURL2 text,
  snDateTime datetime NOT NULL,
  snIsArchived int(1) NOT NULL DEFAULT '0',
  snIsNew int(1) NOT NULL DEFAULT '0',
  snTitle varchar(255) DEFAULT NULL,
  snDescription text,
  snBody text,
  PRIMARY KEY (snID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE TaskPermissions (
  tpID int(10) unsigned NOT NULL AUTO_INCREMENT,
  tpHandle varchar(255) DEFAULT NULL,
  tpName varchar(255) DEFAULT NULL,
  tpDescription text,
  pkgID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (tpID),
  UNIQUE KEY tpHandle (tpHandle)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE TaskPermissionUserGroups (
  tpID int(10) unsigned NOT NULL DEFAULT '0',
  gID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  canRead int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (tpID,gID,uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserAttributeKeys (
  akID int(10) unsigned NOT NULL,
  uakProfileDisplay tinyint(1) NOT NULL DEFAULT '0',
  uakMemberListDisplay tinyint(1) NOT NULL DEFAULT '0',
  uakProfileEdit tinyint(1) NOT NULL DEFAULT '1',
  uakProfileEditRequired tinyint(1) NOT NULL DEFAULT '0',
  uakRegisterEdit tinyint(1) NOT NULL DEFAULT '0',
  uakRegisterEditRequired tinyint(1) NOT NULL DEFAULT '0',
  displayOrder int(10) unsigned DEFAULT '0',
  uakIsActive tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (akID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserAttributeValues (
  uID int(10) unsigned NOT NULL DEFAULT '0',
  akID int(10) unsigned NOT NULL DEFAULT '0',
  avID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uID,akID,avID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserBannedIPs (
  ipFrom int(10) unsigned NOT NULL DEFAULT '0',
  ipTo int(10) unsigned NOT NULL DEFAULT '0',
  banCode int(1) unsigned NOT NULL DEFAULT '1',
  expires int(10) unsigned NOT NULL DEFAULT '0',
  isManual int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (ipFrom,ipTo),
  KEY ipFrom (ipFrom),
  KEY ipTo (ipTo)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserGroups (
  uID int(10) unsigned NOT NULL DEFAULT '0',
  gID int(10) unsigned NOT NULL DEFAULT '0',
  ugEntered datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `type` varchar(64) DEFAULT NULL,
  PRIMARY KEY (uID,gID),
  KEY uID (uID),
  KEY gID (gID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserOpenIDs (
  uID int(10) unsigned NOT NULL,
  uOpenID varchar(255) NOT NULL,
  PRIMARY KEY (uOpenID),
  KEY uID (uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserPrivateMessages (
  msgID int(10) unsigned NOT NULL AUTO_INCREMENT,
  uAuthorID int(10) unsigned NOT NULL DEFAULT '0',
  msgDateCreated datetime NOT NULL,
  msgSubject varchar(255) NOT NULL,
  msgBody text,
  uToID int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (msgID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserPrivateMessagesTo (
  msgID int(10) unsigned NOT NULL DEFAULT '0',
  uID int(10) unsigned NOT NULL DEFAULT '0',
  uAuthorID int(10) unsigned NOT NULL DEFAULT '0',
  msgMailboxID int(11) NOT NULL,
  msgIsNew int(1) NOT NULL DEFAULT '0',
  msgIsUnread int(1) NOT NULL DEFAULT '0',
  msgIsReplied int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (msgID,uID,uAuthorID),
  KEY uID (uID),
  KEY uAuthorID (uAuthorID),
  KEY msgFolderID (msgMailboxID),
  KEY msgIsNew (msgIsNew)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE Users (
  uID int(10) unsigned NOT NULL AUTO_INCREMENT,
  uName varchar(64) NOT NULL,
  uEmail varchar(64) NOT NULL,
  uPassword varchar(255) NOT NULL,
  uIsActive varchar(1) NOT NULL DEFAULT '0',
  uIsValidated tinyint(4) NOT NULL DEFAULT '-1',
  uIsFullRecord tinyint(1) NOT NULL DEFAULT '1',
  uDateAdded datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  uHasAvatar tinyint(1) NOT NULL DEFAULT '0',
  uLastOnline int(10) unsigned NOT NULL DEFAULT '0',
  uLastLogin int(10) unsigned NOT NULL DEFAULT '0',
  uPreviousLogin int(10) unsigned NOT NULL DEFAULT '0',
  uNumLogins int(10) unsigned NOT NULL DEFAULT '0',
  uTimezone varchar(255) DEFAULT NULL,
  uDefaultLanguage varchar(32) DEFAULT NULL,
  PRIMARY KEY (uID),
  UNIQUE KEY uName (uName)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserSearchIndexAttributes (
  uID int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UsersFriends (
  ufID int(10) unsigned NOT NULL AUTO_INCREMENT,
  uID int(10) unsigned DEFAULT NULL,
  `status` varchar(64) NOT NULL,
  friendUID int(10) unsigned DEFAULT NULL,
  uDateAdded datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (ufID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE UserValidationHashes (
  uvhID int(10) unsigned NOT NULL AUTO_INCREMENT,
  uID int(10) unsigned DEFAULT NULL,
  uHash varchar(64) NOT NULL,
  `type` int(4) unsigned NOT NULL DEFAULT '0',
  uDateGenerated int(10) unsigned NOT NULL DEFAULT '0',
  uDateRedeemed int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (uvhID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
