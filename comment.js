#!/usr/bin/env node

const bot = require("/circle-github-bot-master").create();

bot.comment(`
<p>Commit message: ${bot.env.commitMessage}</p>
<p>${bot.artifactLink('app/build/summary.html', 'Visual Diffs')}</p>
<p>${bot.artifactLink('app/build/styleGuide.html', 'Style Guide')}</p>
<p>${bot.artifactLink('app/build/head/cssAnalysis.txt', 'CSS Analysis')}</p>
`);