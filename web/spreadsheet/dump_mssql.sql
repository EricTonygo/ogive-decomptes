CREATE TABLE "dhx_data" (
  "sheetid" varchar(255) DEFAULT NULL,
  "columnid" int DEFAULT NULL,
  "rowid" int DEFAULT NULL,
  "data" varchar(255) DEFAULT NULL,
  "style" varchar(255) DEFAULT NULL,
  "parsed" varchar(255) DEFAULT NULL,
  "calc" varchar(255) DEFAULT NULL,
  PRIMARY KEY ("sheetid","columnid","rowid")
);


CREATE TABLE "dhx_header" (
  "sheetid" varchar(255) DEFAULT NULL,
  "columnid" int DEFAULT NULL,
  "label" varchar(255) DEFAULT NULL,
  "width" int DEFAULT NULL,
  PRIMARY KEY ("sheetid","columnid")
);


CREATE TABLE "dhx_sheet" (
  "sheetid" varchar(255) NOT NULL,
  "userid" int DEFAULT NULL,
  "name" varchar(255) DEFAULT NULL,
  "key" varchar(255) DEFAULT NULL,
  "cfg" varchar(512) DEFAULT NULL,
  PRIMARY KEY ("sheetid")
);

INSERT INTO "dhx_sheet" VALUES ('demo_sheet', null, null, 'any_key', null);


CREATE TABLE "dhx_user" (
  "userid" int NOT NULL  IDENTITY(1,1),
  "apikey" varchar(255) DEFAULT NULL,
  "email" varchar(255) DEFAULT NULL,
  "secret" varchar(64) DEFAULT NULL,
  "pass" varchar(64) DEFAULT NULL,
  PRIMARY KEY ("userid")
);


CREATE TABLE "dhx_triggers" (
  "id" int NOT NULL  IDENTITY(1,1),
  "sheetid" varchar(255) DEFAULT NULL,
  "trigger" varchar(10) DEFAULT NULL,
  "source" varchar(10) DEFAULT NULL,
  PRIMARY KEY ("id")
);