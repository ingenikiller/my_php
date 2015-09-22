<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:variable name="BLANK">application/xsl/blank.html</xsl:variable>

<xsl:variable name="CINEMATIC">
	<xsl:value-of select="/root/request/cinematic"/>
</xsl:variable>

<xsl:variable name="UTI.DROITS">
	<xsl:value-of select="/root/user/droits"/>
</xsl:variable>

<xsl:param name="NUMEROCOMPTE">
		<xsl:value-of select="/root/request/numeroCompte"/>
</xsl:param>

</xsl:stylesheet>
