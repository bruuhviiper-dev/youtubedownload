const { chromium } = require('playwright');
const fs = require('fs');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  console.log('Acessando login do Google...');
  await page.goto('https://accounts.google.com/signin');

  // Email
  await page.fill('input[type="email"]', process.env.GOOGLE_EMAIL);
  await page.click('#identifierNext');
  await page.waitForTimeout(2000);

  // Senha
  try {
      await page.fill('input[type="password"]', process.env.GOOGLE_PASSWORD);
      await page.click('#passwordNext');
      await page.waitForTimeout(4000);
  } catch (e) {
      console.log('Erro no passo da senha. Tentando seletor alternativo...');
      await page.fill('input[name="password"]', process.env.GOOGLE_PASSWORD);
      await page.click('#passwordNext');
      await page.waitForTimeout(4000);
  }

  // Vai pro YouTube para gerar cookies válidos
  console.log('Acessando YouTube...');
  await page.goto('https://www.youtube.com');
  await page.waitForTimeout(5000);

  // Exporta os cookies no formato Netscape (aceito pelo yt-dlp)
  const cookies = await context.cookies(['https://www.youtube.com', 'https://google.com']);
  
  let cookieFile = '# Netscape HTTP Cookie File\n';
  for (const cookie of cookies) {
    const domain = cookie.domain.startsWith('.') ? cookie.domain : '.' + cookie.domain;
    const secure = cookie.secure ? 'TRUE' : 'FALSE';
    const httpOnly = cookie.httpOnly ? 'TRUE' : 'FALSE';
    const expires = cookie.expires > 0 ? Math.round(cookie.expires) : 0;
    
    cookieFile += `${domain}\tTRUE\t${cookie.path}\t${secure}\t${expires}\t${cookie.name}\t${cookie.value}\n`;
  }

  fs.writeFileSync('/tmp/cookies.txt', cookieFile);
  console.log(`✅ ${cookies.length} cookies exportados com sucesso.`);

  await browser.close();
})();
