<?xml version="1.0" encoding="UTF-8" ?>
<XMLDB PATH="auth/twiliootp/db" VERSION="20170323" COMMENT="XMLDB file for Moodle auth/twiliootp"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="../../../lib/xmldb/xmldb.xsd"
>
  <TABLES>
    <TABLE NAME="auth_twiliootp_create" COMMENT="Accounts linked to a users Moodle account.">
      <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="username" TYPE="text" NOTNULL="true" SEQUENCE="false" COMMENT="The username to map to this moodle account."/>
        <FIELD NAME="email" TYPE="text" NOTNULL="true" SEQUENCE="false" COMMENT="The email to map to this moodle account"/>
        <FIELD NAME="phone" TYPE="text" NOTNULL="true" SEQUENCE="false" COMMENT="The phone2 to map to this moodle account"/>
        <FIELD NAME="countrycode" TYPE="text" NOTNULL="true" SEQUENCE="false"/>
        <FIELD NAME="otp_code" TYPE="int" LENGTH="4" NOTNULL="true" SEQUENCE="false"/>
        <FIELD NAME="otp_expiry" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
        <FIELD NAME="verification_status" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
        <FIELD NAME="otpcreated" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
        <FIELD NAME="attempts_count" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="false"/>
      </FIELDS>
      <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
      </KEYS>
    </TABLE>
  </TABLES>
</XMLDB>
