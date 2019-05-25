import React, { Component } from 'react';
import { connect } from 'react-redux';

const Main = connect((state) => {
  return {
    // ...
  };
}, (dispatch) => {
  return {
    // ...
  };
})(class extends Component {

  /**
   * Render the {React} component.
   *
   * @returns {void}
   */
  render() {
    return (
      <div>
        <p>Welcome!</p>
      </div>
    );
  }

});

export default Main;