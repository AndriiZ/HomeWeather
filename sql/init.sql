SET SQL DIALECT 3;

SET NAMES UTF8;

CREATE DATABASE 'WEATHER'
USER 'WEATHER'
PAGE_SIZE 16384
DEFAULT CHARACTER SET UTF8 COLLATION UTF8;



/******************************************************************************/
/***                               Generators                               ***/
/******************************************************************************/

CREATE GENERATOR GEN_MEASUREMENTTYPES_ID;
CREATE GENERATOR GEN_SENSORS_ID;


/******************************************************************************/
/***                           Stored procedures                            ***/
/******************************************************************************/



SET TERM ^ ;

CREATE PROCEDURE GETDATEFROMMNEMONIC (
    MNEMONIC VARCHAR(16) = null)
RETURNS (
    STARTTIME TIMESTAMP)
AS
BEGIN
  SUSPEND;
END^





CREATE PROCEDURE LAST_MEASUREMENT (
    SENSORGUID CHAR(36) NOT NULL,
    MEASURENAME VARCHAR(16) NOT NULL = 'humidity')
RETURNS (
    MEASUREMENTTIME TIMESTAMP,
    MEASUREMENTNAME VARCHAR(16),
    MEASUREMENTVALUE DECIMAL(6,2))
AS
BEGIN
  SUSPEND;
END^





CREATE PROCEDURE LISTOFMEASUREMENTSFORSENSOR (
    SENSORGUID CHAR(36) NOT NULL,
    MEASURENAME VARCHAR(16) NOT NULL,
    STARTDATE VARCHAR(16) = 'today')
RETURNS (
    MEASUREMENT_TIME TIMESTAMP,
    MEASURE DECIMAL(15,2),
    MEASUREMENT_TIMEUNIX BIGINT)
AS
BEGIN
  SUSPEND;
END^





CREATE PROCEDURE NEW_MEASUREMENT (
    SENSORGUID CHAR(36) NOT NULL,
    MEASUREMENTTYPE VARCHAR(16) NOT NULL,
    MEASUREMENTVALUE DECIMAL(6,2) NOT NULL)
RETURNS (
    MEASUREMENT_TIME TIMESTAMP)
AS
BEGIN
  SUSPEND;
END^






SET TERM ; ^



/******************************************************************************/
/***                                 Tables                                 ***/
/******************************************************************************/



CREATE TABLE MEASUREMENTS (
    MEASUREMENT_TIME   TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    MEASUREMENTTYPEID  INTEGER NOT NULL,
    SENSORID           INTEGER NOT NULL,
    MEASURE            DECIMAL(6,2) NOT NULL
);


CREATE TABLE MEASUREMENTTYPES (
    TYPEID       INTEGER NOT NULL,
    TYPENAME     VARCHAR(32) NOT NULL,
    "PRECISION"  SMALLINT DEFAULT 2 NOT NULL
);


CREATE TABLE SENSORS (
    SENSORID    INTEGER NOT NULL,
    NAME        VARCHAR(32) NOT NULL,
    "DESC"      VARCHAR(255),
    SENSORUUID  CHAR(16) CHARACTER SET OCTETS
);


INSERT INTO MEASUREMENTTYPES (TYPEID, TYPENAME, "PRECISION") VALUES (1, 'Temperature', 2);
INSERT INTO MEASUREMENTTYPES (TYPEID, TYPENAME, "PRECISION") VALUES (2, 'Humidity', 2);
INSERT INTO MEASUREMENTTYPES (TYPEID, TYPENAME, "PRECISION") VALUES (3, 'Pressure', 2);

COMMIT WORK;



/******************************************************************************/
/***                              Primary keys                              ***/
/******************************************************************************/

ALTER TABLE MEASUREMENTS ADD CONSTRAINT PK_MEASUREMENTS PRIMARY KEY (MEASUREMENT_TIME, MEASUREMENTTYPEID, SENSORID);
ALTER TABLE MEASUREMENTTYPES ADD CONSTRAINT PK_MEASUREMENTTYPES PRIMARY KEY (TYPEID);
ALTER TABLE SENSORS ADD CONSTRAINT PK_SENSORS PRIMARY KEY (SENSORID);


/******************************************************************************/
/***                              Foreign keys                              ***/
/******************************************************************************/

ALTER TABLE MEASUREMENTS ADD CONSTRAINT FK_MEASUREMENTS_SENSORID FOREIGN KEY (SENSORID) REFERENCES SENSORS (SENSORID);
ALTER TABLE MEASUREMENTS ADD CONSTRAINT FK_MEASUREMENTS_TYPEID FOREIGN KEY (MEASUREMENTTYPEID) REFERENCES MEASUREMENTTYPES (TYPEID);


/******************************************************************************/
/***                                Triggers                                ***/
/******************************************************************************/



SET TERM ^ ;



/******************************************************************************/
/***                          Triggers for tables                           ***/
/******************************************************************************/



/* Trigger: MEASUREMENTTYPES_BI */
CREATE TRIGGER MEASUREMENTTYPES_BI FOR MEASUREMENTTYPES
ACTIVE BEFORE INSERT POSITION 0
as
begin
  if (new.typeid is null) then
    new.typeid = gen_id(gen_measurementtypes_id,1);
end
^


/* Trigger: SENSORS_BI */
CREATE TRIGGER SENSORS_BI FOR SENSORS
ACTIVE BEFORE INSERT POSITION 0
as
begin
  if (new.sensorid is null) then
    new.sensorid = gen_id(gen_sensors_id,1);
  if (new.sensoruuid is null) then
    new.sensoruuid = gen_uuid();
end
^

SET TERM ; ^



/******************************************************************************/
/***                           Stored procedures                            ***/
/******************************************************************************/



SET TERM ^ ;

ALTER PROCEDURE GETDATEFROMMNEMONIC (
    MNEMONIC VARCHAR(16) = null)
RETURNS (
    STARTTIME TIMESTAMP)
AS
begin
 if (mnemonic is null) then
    mnemonic = 'hour';
 if (mnemonic = 'today') then
    SELECT current_date from rdb$database INTO :STARTTIME;

  if (mnemonic = 'hour') then
    SELECT dateadd(hour, -1, current_timestamp) from rdb$database INTO :STARTTIME;
  if (mnemonic = '3hour') then
    SELECT dateadd(hour, -3, current_timestamp) from rdb$database INTO :STARTTIME;
  if (mnemonic = 'dbn') then
    SELECT dateadd(day, -1, current_timestamp) from rdb$database INTO :STARTTIME;
  if (mnemonic = '3day') then
    SELECT dateadd(day, -3, current_timestamp) from rdb$database INTO :STARTTIME;
  if (mnemonic = 'week') then
  begin
     if (EXTRACT(weekday from current_date) = 0) then
        SELECT dateadd(day, -6, current_date) from rdb$database INTO :STARTTIME;
     else
        SELECT dateadd(day, -(EXTRACT(weekday from current_date)-1), current_date) from rdb$database INTO :STARTTIME;
  end
  if (mnemonic = 'wtn') then
    SELECT dateadd(week, -1, current_timestamp) from rdb$database INTO :STARTTIME;
  if (mnemonic = 'mtn') then
    SELECT dateadd(month, -1, current_timestamp) from rdb$database INTO :STARTTIME;
  if (mnemonic = 'month') then
    SELECT dateadd(day, -(EXTRACT(DAY from current_date)-1), current_date) from rdb$database INTO :STARTTIME;

  if (mnemonic = 'ytn') then
    SELECT dateadd(year, -1, current_timestamp) from rdb$database INTO :STARTTIME;
  if (mnemonic = 'year') then
    SELECT dateadd(day, -(EXTRACT(YEARDAY from current_date)), current_date) from rdb$database INTO :STARTTIME;
  if (mnemonic similar to '[0-9]+') then
       select dateadd(second, 1+CAST(:mnemonic as bigint)/1000 , timestamp '1970-01-01 02:00:00') from rdb$database INTO :STARTTIME;
  suspend;
end^


ALTER PROCEDURE LAST_MEASUREMENT (
    SENSORGUID CHAR(36) NOT NULL,
    MEASURENAME VARCHAR(16) NOT NULL = 'humidity')
RETURNS (
    MEASUREMENTTIME TIMESTAMP,
    MEASUREMENTNAME VARCHAR(16),
    MEASUREMENTVALUE DECIMAL(6,2))
AS
declare variable SENSORID integer;
declare variable MEASURETYPEID integer;
begin
  SELECT s.sensorid FROM sensors s
  WHERE s.sensoruuid = CHAR_TO_UUID(:SENSORGUID)
  INTO :sensorid;

  SELECT mt.typeid from measurementtypes mt
  WHERE UPPER(mt.typename) = UPPER(:measurename)
  into :MEASURETYPEID;

  select first 1 m.measurement_time, mt.typename, m.measure
  from MEASUREMENTS m
  INNER JOIN measurementtypes mt  on m.measurementtypeid = mt.typeid
  WHERE m.sensorid=:sensorid and m.measurementtypeid = :MEASURETYPEID
  ORDER BY MEASUREMENT_TIME DESC
  into :measurementtime, :measurementname, :measurementvalue;
  suspend;
end^


ALTER PROCEDURE LISTOFMEASUREMENTSFORSENSOR (
    SENSORGUID CHAR(36) NOT NULL,
    MEASURENAME VARCHAR(16) NOT NULL,
    STARTDATE VARCHAR(16) = 'today')
RETURNS (
    MEASUREMENT_TIME TIMESTAMP,
    MEASURE DECIMAL(15,2),
    MEASUREMENT_TIMEUNIX BIGINT)
AS
declare variable MEASURETYPEID integer;
declare variable SENSORID integer;
declare variable STARTTIME timestamp;
begin
  SELECT s.sensorid FROM sensors s
  WHERE s.sensoruuid = CHAR_TO_UUID(:SENSORGUID)
  INTO :sensorid;

  SELECT mt.typeid from measurementtypes mt
  WHERE UPPER(mt.typename) = UPPER(:measurename)
  into :MEASURETYPEID;

   select STARTTIME
   from GETDATEFROMMNEMONIC(:startdate)
   into :STARTTIME;

  for select m.measurement_time, m.measure, DATEDIFF(millisecond, timestamp '1970-01-01 02:00:00', m.measurement_time)
  from MEASUREMENTS m
  INNER JOIN measurementtypes mt  on m.measurementtypeid = mt.typeid
  WHERE m.sensorid=:sensorid and m.measurementtypeid = :MEASURETYPEID
  AND m.measurement_time >=:STARTTIME
  ORDER BY MEASUREMENT_TIME
  into :measurement_time, :measure, measurement_timeunix
  do suspend;
end^


ALTER PROCEDURE NEW_MEASUREMENT (
    SENSORGUID CHAR(36) NOT NULL,
    MEASUREMENTTYPE VARCHAR(16) NOT NULL,
    MEASUREMENTVALUE DECIMAL(6,2) NOT NULL)
RETURNS (
    MEASUREMENT_TIME TIMESTAMP)
AS
declare variable MEASUREMENTID integer;
declare variable SENSORID integer;
begin
  SELECT s.sensorid FROM sensors s
  WHERE s.sensoruuid = CHAR_TO_UUID(:SENSORGUID)
  INTO :sensorid;

  SELECT mt.typeid FROM measurementtypes mt
  WHERE LOWER(mt.typename) = LOWER(:measurementtype)
  INTO :measurementid;

  IF (:sensorid IS NOT NULL AND :measurementid IS NOT NULL) THEN
  BEGIN
     INSERT INTO measurements (SENSORID,MEASUREMENTTYPEID,MEASURE)
     VALUES(:sensorid, :measurementid, :MEASUREMENTVALUE)
     RETURNING MEASUREMENT_TIME INTO :MEASUREMENT_TIME;
  END
  suspend;
end^



SET TERM ; ^

