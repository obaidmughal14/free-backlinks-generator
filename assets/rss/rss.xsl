<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="yes"/>
	<xsl:template match="/">
		<html lang="en">
			<head>
				<meta charset="utf-8"/>
				<meta name="viewport" content="width=device-width, initial-scale=1"/>
				<title>RSS feed</title>
				<style type="text/css">
					body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;margin:0;background:#0f172a;color:#e2e8f0;line-height:1.5}
					.wrap{max-width:720px;margin:0 auto;padding:2rem 1.25rem 3rem}
					h1{font-size:1.35rem;margin:0 0 .25rem;color:#fff}
					.sub{color:#94a3b8;font-size:.9rem;margin:0 0 1.75rem}
					.badge{display:inline-block;background:rgba(79,142,247,.2);color:#93c5fd;padding:.2rem .65rem;border-radius:999px;font-size:.75rem;font-weight:600;margin-bottom:1.5rem}
					.item{background:#1e293b;border:1px solid #334155;border-radius:12px;padding:1.1rem 1.25rem;margin-bottom:1rem}
					.item a{color:#7dd3fc;text-decoration:none;font-weight:600;font-size:1.05rem}
					.item a:hover{text-decoration:underline}
					.meta{color:#94a3b8;font-size:.8rem;margin:.5rem 0 0}
					.desc{color:#cbd5e1;font-size:.9rem;margin:.65rem 0 0}
					.foot{margin-top:2rem;padding-top:1.25rem;border-top:1px solid #334155;font-size:.8rem;color:#64748b}
					.foot a{color:#4f8ef7}
				</style>
			</head>
			<body>
				<div class="wrap">
					<span class="badge">RSS 2.0</span>
					<h1>
						<xsl:value-of select="/rss/channel/title"/>
					</h1>
					<p class="sub">
						<xsl:value-of select="/rss/channel/description"/>
					</p>
					<xsl:for-each select="/rss/channel/item">
						<div class="item">
							<a>
								<xsl:attribute name="href">
									<xsl:value-of select="link"/>
								</xsl:attribute>
								<xsl:value-of select="title"/>
							</a>
							<p class="meta">
								<xsl:value-of select="pubDate"/>
							</p>
							<xsl:if test="description">
								<p class="desc">
									<xsl:value-of select="description" disable-output-escaping="yes"/>
								</p>
							</xsl:if>
						</div>
					</xsl:for-each>
					<p class="foot">Subscribe with a feed reader. New items appear as soon as they are published.</p>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
