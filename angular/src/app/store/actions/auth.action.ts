import { Action } from '@ngrx/store';

export const AUTH = '[Auth] Login';
export const AUTH_COMPLETED = '[Auth] Login Completed';
export const AUTH_FAILED = '[Auth] Login Failed';
export const VALIDATE_TOKEN = '[Auth] Validate Token - Get info from token';
export const LOGOUT = '[Auth] Logout';
export const LOGOUT_SUCCESS = '[Auth] Logout Success';

export class Auth implements Action {
  readonly type = AUTH;
  constructor(public id, public password) { }
}

export class AuthCompleted implements Action {
  readonly type = AUTH_COMPLETED;
  constructor(public payload) { }
}

export class AuthFailed implements Action {
  readonly type = AUTH_FAILED;
  constructor(public err) { }
}

export class ValidateToken implements Action {
  readonly type = VALIDATE_TOKEN;
}

export class Logout implements Action {
  readonly type = LOGOUT;
}

export class LogoutSuccess implements Action {
  readonly type = LOGOUT_SUCCESS;
}

export type All = Auth | AuthCompleted | AuthFailed | ValidateToken | Logout | LogoutSuccess;
