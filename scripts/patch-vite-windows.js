import fs from 'node:fs';
import path from 'node:path';

if (process.platform !== 'win32') {
    process.exit(0);
}

const target = path.resolve('node_modules/vite/dist/node/chunks/config.js');

if (!fs.existsSync(target)) {
    process.exit(0);
}

const source = fs.readFileSync(target, 'utf8');
const search = `\texec("net use", (error$1, stdout) => {\n\t\tif (error$1) return;\n\t\tconst lines = stdout.split("\\n");\n\t\tfor (const line of lines) {\n\t\t\tconst m = parseNetUseRE.exec(line);\n\t\t\tif (m) windowsNetworkMap.set(m[2], m[1]);\n\t\t}\n\t\tif (windowsNetworkMap.size === 0) safeRealpathSync = fs.realpathSync.native;\n\t\telse safeRealpathSync = windowsMappedRealpathSync;\n\t});`;
const replacement = `\tsafeRealpathSync = fs.realpathSync.native;`;

if (!source.includes(search)) {
    process.exit(0);
}

fs.writeFileSync(target, source.replace(search, replacement));
