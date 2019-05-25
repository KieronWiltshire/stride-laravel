import { createStore, applyMiddleware } from 'redux';
import { composeWithDevTools } from 'redux-devtools-extension';
import logger from 'redux-logger';
import thunk from 'redux-thunk';
import promise from 'redux-promise-middleware';
import { routerMiddleware } from 'react-router-redux';
import rootReducer from './reducers/root';
import { createBrowserHistory } from 'history';

export const history = createBrowserHistory();

export default createStore(rootReducer(history), composeWithDevTools(
  applyMiddleware(promise, thunk, logger, routerMiddleware(history))
));
