<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="commun.xsl"/>
	<xsl:import href="statistiques_commun.xsl"/>
	<xsl:template name="js.module.sheet">
		<script language="JavaScript" src="application/js/statistiques.js" type="text/javascript"/>
	</xsl:template>
	<xsl:template name="controleMenu">N</xsl:template>
	<xsl:template name="Contenu">
		<xsl:call-template name="boiteDetail"/>
		<center>
			<a href="index.php?domaine=statistique&amp;numeroCompte={$NUMEROCOMPTE}">Retour</a><br/>
			<form method="POST" action="#" onsubmit="return soumettreRelevesMois(this);" name="formulaire" id="formulaire">
				<input name="numeroCompte" id="numeroCompte" type="hidden" value="{$NUMEROCOMPTE}"/>
				<table class="formulaire">
					<tr>
						<td>
							<xsl:value-of select="$LBL.PREMIERRELEVE"/>
						</td>
						<td>
							<xsl:apply-templates select="/root/data/ListeReleves">
								<xsl:with-param name="name" select="'premierReleve'"/>
							</xsl:apply-templates>
						</td>
					</tr>
					<tr>
						<td>
							<xsl:value-of select="$LBL.DERNIERRELEVE"/>
						</td>
						<td>
							<xsl:apply-templates select="/root/data/ListeReleves">
								<xsl:with-param name="name" select="'dernierReleve'"/>
							</xsl:apply-templates>
						</td>
					</tr>
				</table>
				<table>
					<tr>
						<td colspan="2" rowspan="1">
							<input name="valider" value="Valider" type="submit"/>
						</td>
					</tr>
				</table>
				<!--<iframe name="frame_resultat" id="frame_resultat" src="" width="100%" height="500"/>-->
				<table id="tableResultat" name="tableResultat" class="formulaire"/>
			</form>
		</center>
	</xsl:template>
	<xsl:template match="ListeReleves">
		<xsl:param name="name"/>
		<select name="{$name}" id="{$name}">
			<option/>
			<xsl:apply-templates select="Dynamic"/>
		</select>
	</xsl:template>
	<xsl:template match="Dynamic">
		<option value="{noreleve}">
			<xsl:value-of select="noreleve"/>
		</option>
	</xsl:template>
</xsl:stylesheet>
