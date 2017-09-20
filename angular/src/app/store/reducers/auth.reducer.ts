import { AppState } from './index';
import { ActionReducer } from '@ngrx/store';
import * as authAction from '../actions/auth.action';

export type Action = authAction.All;

/**
 * Define the state for Auth
 */
export interface AuthState {
  loading: boolean;
  currentUser: {
    identityId: string,
    username: string,
  };
  error: string;
}

export const defaultState: AuthState = {
  loading: false,
  currentUser: null,
  error: null
};

/**
 * Define Reducer
 */
export function reducer(state = defaultState, action: Action): AuthState {
  switch (action.type) {
    case authAction.AUTH:
    case authAction.LOGOUT: {
      return Object.assign({}, state, {
        currentUser: null,
        loading: true,
        error: null
      });
    }
    case authAction.AUTH_COMPLETED: {
      return Object.assign({}, state, {
        currentUser: action.user,
        loading: false,
        error: null
      });
    }
    case authAction.AUTH_FAILED: {
      return Object.assign({}, state, {
        currentUser: null,
        loading: false,
        error: action.err,
      });
    }
    case authAction.LOGOUT_SUCCESS: {
      return Object.assign({}, state, {
        currentUser: null,
        loading: false,
        error: null
      });
    }
    default: {
      return state;
    }
  }
};
