<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
	<!-- 
		liste flux
	-->
	<xsl:template name="ListeFlux">
		<xsl:param name="liste"/>
		<xsl:param name="champ"/>
		<xsl:param name="valeur"/>
		<xsl:param name="obligatoire" select="'N'"/>
		<xsl:param name="class" select="''"/>
		<xsl:param name="valeurVide" select="'O'"/>
		<xsl:param name="tabindex" select="''"/>
		<xsl:param name="onchange" select="''"/>
		<select name="{$champ}" id="{$champ}" class="{$class}">
			<xsl:if test="$obligatoire='true'">
				<xsl:attribute name="class">obligatoire</xsl:attribute>
			</xsl:if>
			<xsl:if test="$tabindex!=''">
				<xsl:attribute name="tabindex"><xsl:value-of select="$tabindex"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="$onchange!=''">
				<xsl:attribute name="onchange"><xsl:value-of select="$onchange"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="$valeurVide='' or $valeurVide='O'">
				<option/>
			</xsl:if>
			<xsl:for-each select="$liste/Flux">
				<option value="{fluxId}">
					<xsl:if test="$valeur=fluxId">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="flux"/>
				</option>
			</xsl:for-each>
		</select>
	</xsl:template>
	
	<!--
		affichage montant
	-->
	<xsl:template name="Montant">
		<xsl:param name="montant" select="'0'"/>
		<xsl:value-of select="format-number($montant, $FORMAT_MNT)"/>
	</xsl:template>
	<!-- 
		Modif select
	-->
	<xsl:template name="ModifSelect">
		<xsl:param name="value" select="''"/>
		<xsl:param name="Node"/>
		<xsl:param name="nom"/>
		<xsl:param name="defaultValue"/>
		<xsl:param name="defaultDisplay"/>
		<xsl:param name="onChange" select="''"/>
		<xsl:param name="class" select="''"/>
		<xsl:param name="tabindex" select="''"/>
		<xsl:param name="optionVide" select='O'/>
		<select name="{$nom}" id="{$nom}" class="{$class}">
			<xsl:if test="$onChange!=''">
				<xsl:attribute name="onchange">
					<xsl:value-of select="$onChange"/>
				</xsl:attribute>
			</xsl:if>
			<xsl:if test="$tabindex!=''">
				<xsl:attribute name="tabindex"><xsl:value-of select="$tabindex"/></xsl:attribute>
			</xsl:if>
			<xsl:if test="$optionVide='O'">
				<option/>
			</xsl:if>
			<xsl:if test="$defaultValue!=''">
				<option value="{$defaultValue}">
					<xsl:value-of select="$defaultDisplay"/>
				</option>
			</xsl:if>
			<xsl:for-each select="$Node/Segment">
				<xsl:choose>
					<xsl:when test="$value=codseg">
						<option value="{codseg}" selected="selected">
							<xsl:value-of select="liblong"/>
						</option>
					</xsl:when>
					<xsl:otherwise>
						<option value="{codseg}">
							<xsl:value-of select="liblong"/>
						</option>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</select>
	</xsl:template>
	<!-- 
		liste mois
	-->
	<xsl:template name="SelectMois">
		<xsl:param name="name"/>
		<xsl:param name="obligatoire" select="'N'"/>
		<select name="{$name}" id="{$name}">
			<xsl:if test="$obligatoire='O'">
				<xsl:attribute name="class">obligatoire</xsl:attribute>
			</xsl:if>
			<option></option>
			<option value="01" >Janvier</option>
			<option value="02" >F�vrier</option>
			<option value="03" >Mars</option>
			<option value="04" >Avril</option>
			<option value="05" >Mai</option>
			<option value="06" >Juin</option>
			<option value="07" >Juillet</option>
			<option value="08" >Ao�t</option>
			<option value="09" >Septembre</option>
			<option value="10" >Octobre</option>
			<option value="11" >Novembre</option>
			<option value="12" >D�cembre</option>
		</select>
	</xsl:template>
	<!--
		�dition d'une op�ration
	-->
	<xsl:template name="operationEdition">
		<xsl:param name="numeroCompte"/>
		<div id="boiteOperation" title="{$LBL.EDITIONOPERATION}" style="display: none;">
		<center>
			<form method="POST" action="#" onsubmit="return soumettre(this);" name="operation" id="operation">
				<input type="hidden" name="service" id="service"/>
				<input type="hidden" id="noCompte" name="noCompte" value="{$numeroCompte}"/>
				<input type="hidden" name="operationId" id="operationId" value=""/>
				<table class="formulaire">
					<tbody>
						<tr>
							<th style="width: 266px;">
								<xsl:value-of select="$LBL.NUMERORELEVE"/>
							</th>
							<td style="width: 445px;">
								<input size="12" name="noReleve" id="noReleve"  tabindex="10" value="{/root/data/Operation/noReleve}"/>
							</td>
						</tr>
						<tr>
							<th style="width: 266px;">
								<xsl:value-of select="$LBL.DATE"/>
							</th>
							<td style="width: 445px;">
								<input type="text" name="date" id="date" size="11" maxlength="10" tabindex="20" value="{/root/data/Operation/date}"/>
							</td>
						</tr>
						<tr>
							<th style="width: 266px;">
								<xsl:value-of select="$LBL.LIBELLE"/>
							</th>
							<td style="width: 445px;">
								<input type="text" size="50" id="libelle"  tabindex="30" value="{/root/data/Operation/libelle}"/>
							</td>
						</tr>
						<tr>
							<th style="width: 266px;">
								<xsl:value-of select="$LBL.FLUX"/>
							</th>
							<td style="width: 445px;">
								<select name="fluxId" id="fluxId" class="obligatoire" onchange="return getModeReglementDefaut(this, this.form.modePaiementId)" tabindex="40"/>
							</td>
						</tr>
						<tr>
							<th>
								<xsl:value-of select="$LBL.MODEDEPAIEMENT"/>
							</th>
							<td>
								<xsl:call-template name="ModifSelect">
									<xsl:with-param name="value" select="/root/data/Operation/modePaiementId"/>
									<xsl:with-param name="Node" select="/root/paramFlow/MODPAI"/>
									<xsl:with-param name="nom" select="'modePaiementId'"/>
									<xsl:with-param name="defaultValue" select="''"/>
									<xsl:with-param name="defaultDisplay" select="''"/>
									<xsl:with-param name="optionVide" select="'O'"/>
									<xsl:with-param name="tabindex" select="'50'"/>
								</xsl:call-template>
							</td>
						</tr>
						<tr>
							<th style="width: 266px;">
								<xsl:value-of select="$LBL.MONTANT"/>
							</th>
							<td style="width: 445px;">
								<input size="7" name="montant" id="montant" class="numerique obligatoire" onblur="return isDouble(this);" tabindex="60">
									<xsl:attribute name="value">
										<xsl:choose>
											<xsl:when test="/root/data/Operation/montant">
												<xsl:call-template name="Montant">
													<xsl:with-param name="montant" select="/root/data/Operation/montant"/>
												</xsl:call-template>
											</xsl:when>
											<xsl:otherwise>0</xsl:otherwise>
										</xsl:choose>
									</xsl:attribute>
								</input>
							</td>
						</tr>
						<tr>
							<th style="width: 266px;">
								<xsl:value-of select="$LBL.VERIFICATION"/>
							</th>
							<td style="width: 445px;">
								<input type="checkbox" name="verif" id="verif" checked="Verif" tabindex="70" />
							</td>
						</tr>
						<tr align="center">
							<td colspan="2" rowspan="1">
								<input name="valider" value="Valider" type="submit"/>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
		</center>
		</div>
	</xsl:template>
	
	<!--
		�dition d'un compte
	-->
	<xsl:template name="compteEdition">
		<div id="boiteCompte" title="{$LBL.EDITIONCOMPTE}" style="display: none;">
			<center>
				<br/>
				<form method="POST" action="#" onsubmit="return soumettre(this);">
					<input type="hidden" name="service" id="service"/>
					<table class="formulaire">
						<tr>
							<th>
								<xsl:value-of select="$LBL.NUMEROCOMPTE"/>
							</th>
							<td>
								<input type="text" name="numeroCompte" id="numeroCompte"/>
							</td>
						</tr>
						<tr>
							<th>
								<xsl:value-of select="$LBL.DESCRIPTION"/>
							</th>
							<td>
								<input type="text" name="libelle" id="libelle"/>
							</td>
						</tr>
						<tr>
							<th>
								<xsl:value-of select="$LBL.SOLDEBASE"/>
							</th>
							<td>
								<input type="text" name="solde" id="solde" class="numerique" size="10" onblur="return isDouble(this);"/>
							</td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<input class="bouton" type="submit" name="valider" value="Valider"/>
							</td>
						</tr>
					</table>
				</form>
			</center>
		</div>
	</xsl:template>
	
	<!--
		�dition d'un segment
	-->
	<xsl:template name="segmentDetailEdition">
		<div id="boiteSegmentDetail" title="{$LBL.EDITIONSEGMENT}" style="display: none;">
			<center>
				<br/>
				<form method="POST" action="#" onsubmit="return soumettreDetail(this, 'formulaireDetail');" name="segmentDetailForm" id="segmentDetailForm">
					<input type="hidden" name="service" id="service"/>
					<table class="formulaireDetail">
						<tr>
							<th>
								<xsl:value-of select="$LBL.SEGMENT"/>
							</th>
							<td>
								<input type="text" name="cleseg" id="cleseg" readonly="readonly"/>
							</td>
						</tr>
						<tr>
							<th>
								<xsl:value-of select="$LBL.CLE"/>
							</th>
							<td>
								<input type="text" name="codseg" id="codseg" readonly="readonly"/>
							</td>
						</tr>
						<tr>
							<th>
								<xsl:value-of select="$LBL.LIBCOURT"/>
							</th>
							<td>
								<input type="text" name="libcourt" id="libcourt" size="12"/>
							</td>
						</tr>
						<tr>
							<th>
								<xsl:value-of select="$LBL.LIBLONG"/>
							</th>
							<td>
								<input type="text" name="liblong" id="liblong" size="40"/>
							</td>
						</tr>
						<tr align="center">
							<td colspan="2">
								<input class="bouton" type="submit" name="valider" value="{$LBL.VALIDER}"/>
							</td>
						</tr>
					</table>
				</form>
			</center>
		</div>
	</xsl:template>
</xsl:stylesheet>