// The file contents for the current environment will overwrite these during build.
// The build system defaults to the dev environment which uses `environment.ts`, but if you do
// `ng build --env=prod` then `environment.prod.ts` will be used instead.
// The list of which env maps to which file can be found in `.angular-cli.json`.

import { environment as envProd } from './environment.prod';

envProd.production = false;
envProd.webURL = 'http://192.168.10.10:81';
envProd.apiURL = 'http://192.168.10.10:81/v1';

export const environment = envProd
