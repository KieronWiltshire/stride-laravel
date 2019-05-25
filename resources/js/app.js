/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */
import './vendor';

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { default as store, history } from './store';
import registerServiceWorker from './register-service-worker';
import { ConnectedRouter as Router } from 'connected-react-router';
import { Route, Switch } from 'react-router';

import Main from './components/main';
import NotFound from './components/errors/not-found';

/** /
 * |
 * | Application Router.
 * | -------------------------------------
 * | This is the client application router. Here the application
 * | routes are defined which are responsible for navigating
 * | around the application itself.
 * |
 */
ReactDOM.render(
  <Provider store={store}>
    <Router history={history}>
      <Switch>
        <Route exact path="/" component={Main} />
        <Route path="/some/random/path" component={Main} />
        <Route component={NotFound} />
      </Switch>
    </Router>
  </Provider>,
  document.getElementById('root')
);

registerServiceWorker();

