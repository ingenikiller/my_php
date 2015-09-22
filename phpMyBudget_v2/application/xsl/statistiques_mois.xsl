<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:import href="commun.xsl"/>
	<xsl:param name="TYPE">
		<xsl:value-of select="/root/request/type"/>
	</xsl:param>
	<xsl:template match="/">
		<tr>
			<th/>
			<th>
				<xsl:value-of select="$LBL.FLUX"/>
			</th>
			<xsl:for-each select="/root/data/ListeMois/Dynamic">
				<th>
					<xsl:value-of select="mois"/>
				</th>
			</xsl:for-each>
		</tr>
		<xsl:for-each select="/root/data/ListeFlux/Dynamic">
			<xsl:variable name="fluxId" select="fluxId"/>
			<tr class="l{@index mod 2}">
				<th>
					<xsl:if test="fluxMaitre='O'">
						<a onclick="javascript:deplieDetail(this);" replie="O" fluxid="{$fluxId}">
							<img src="{$IMG_ROOT}ftv1plastnode.gif"/>
						</a>
					</xsl:if>
				</th>
				<!-- libelle du flux -->
				<th>
					<xsl:value-of select="flux"/>
					<xsl:if test="operationRecurrente='checked'">(*)</xsl:if>
				</th>
				<!-- chaque mois -->
				<xsl:call-template name="case">
					<xsl:with-param name="fluxId" select="$fluxId"/>
				</xsl:call-template>
				<!--
					cas des flux fils
				-->
				<xsl:for-each select="associatedObjet/ListeFluxFils/Dynamic">
					<xsl:variable name="fluxFils" select="fluxId"/>
					<tr class="cumul" fluxid="{$fluxId}">
						<!--
							affichage de l'icone d'entete
						-->
						<td>
							<xsl:choose>
								<xsl:when test="position()=last()">
									<img src="{$IMG_ROOT}ftv2lastnode.gif"/>
								</xsl:when>
								<xsl:otherwise>
									<img src="{$IMG_ROOT}ftv2node.gif"/>
								</xsl:otherwise>
							</xsl:choose>
						</td>
						<!--
							nom du flux
						-->
						<td>
							<xsl:value-of select="flux"/>
						</td>
						<!--
							pour chaque mois
						-->
						<xsl:for-each select="/root/data/ListeMois/Dynamic">
							<xsl:variable name="mois" select="mois"/>
							<xsl:variable name="valeur" select="/root/data/ListeFlux/Dynamic[fluxId=$fluxId]/associatedObjet/ListeFluxFils/Dynamic[fluxId=$fluxFils]/associatedObjet/MontantFluxFils/Dynamic[date=$mois]/total"/>
							<td class="montant">
								<xsl:if test="$valeur!=''">
									<a href="javascript:afficheDetail('numeroCompte={$NUMEROCOMPTE}&amp;mode=mois&amp;recFlux={$fluxFils}&amp;recDate={mois}')">
									<xsl:value-of select="format-number($valeur,$FORMAT_MNT)"/>
									</a>
								</xsl:if>
							</td>
						</xsl:for-each>
					</tr>
				</xsl:for-each>
			</tr>
		</xsl:for-each>
		<tr>
			<th/>
			<th>   ---</th>
			<xsl:for-each select="/root/data/ListeMois/Dynamic">
				<th/>
			</xsl:for-each>
		</tr>
		<tr class="l0">
			<th/>
			<th>op recurr</th>
			<xsl:for-each select="/root/data/ListeMois/Dynamic">
				<xsl:variable name="valeur" select="associatedObjet/ListeMontantOpeRecurrente/Dynamic/total"/>
				<td class="montant">
					<xsl:if test="$valeur!=''">
						<xsl:value-of select="format-number($valeur,$FORMAT_MNT)"/>
					</xsl:if>
				</td>
			</xsl:for-each>
		</tr>
		<tr class="l1">
			<th/>
			<th>
				<xsl:value-of select="$LBL.EPARGNE"/>
			</th>
			<xsl:for-each select="/root/data/ListeMois/Dynamic">
				<td class="montant">
					<xsl:value-of select="format-number(associatedObjet/ListeMontantEpargne/Dynamic/total,$FORMAT_MNT)"/>
				</td>
			</xsl:for-each>
		</tr>
		<tr class="l0">
			<th/>
			<th>
				<xsl:value-of select="$LBL.TOTALCREDITS"/>
			</th>
			<xsl:for-each select="/root/data/ListeMois/Dynamic">
				<td class="montant">
					<xsl:value-of select="format-number(sum(associatedObjet/ListeMontantFlux/Dynamic[total&gt;0]/total), $FORMAT_MNT)"/>
				</td>
			</xsl:for-each>
		</tr>
		<tr class="l1">
			<th/>
			<th>
				<xsl:value-of select="$LBL.TOTALDEBITS"/>
			</th>
			<xsl:for-each select="/root/data/ListeMois/Dynamic">
				<td class="montant">
					<xsl:value-of select="format-number(sum(associatedObjet/ListeMontantFlux/Dynamic[total&lt;0]/total), $FORMAT_MNT)"/>
				</td>
			</xsl:for-each>
		</tr>
		<tr class="l0">
			<th/>
			<th>
				<xsl:value-of select="$LBL.DIFFERENCE"/>
			</th>
			<xsl:for-each select="/root/data/ListeMois/Dynamic">
				<xsl:variable name="difference" select="sum(associatedObjet/ListeMontantFlux/Dynamic/total)"/>
				<td>
					<xsl:choose>
						<xsl:when test="$difference&gt;0">
							<xsl:attribute name="class">diffPositive</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="class">diffNegative</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
					<xsl:value-of select="format-number($difference, $FORMAT_MNT)"/>
				</td>
			</xsl:for-each>
		</tr>
	</xsl:template>
	<xsl:template name="case">
		<xsl:param name="fluxId"/>
		<xsl:for-each select="/root/data/ListeMois/Dynamic">
			<td align="right">
				<a href="javascript:afficheDetail('numeroCompte={$NUMEROCOMPTE}&amp;mode=mois&amp;recDate={mois}&amp;recFlux={$fluxId}')">
					<xsl:if test="associatedObjet/ListeMontantFlux/Dynamic[fluxId=$fluxId]/total">
						<xsl:value-of select="format-number(associatedObjet/ListeMontantFlux/Dynamic[fluxId=$fluxId]/total,$FORMAT_MNT)"/>
					</xsl:if>
				</a>
			</td>
		</xsl:for-each>
	</xsl:template>
</xsl:stylesheet>
