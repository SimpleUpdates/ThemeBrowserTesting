#!/usr/bin/env node

const bot = require("/circle-github-bot-master").create();

bot.comment(`
<h3>${bot.env.commitMessage}</h3>
Component diffs: <strong>${bot.artifactLink('app/build/summary.html', 'Summary')}</strong>
`);