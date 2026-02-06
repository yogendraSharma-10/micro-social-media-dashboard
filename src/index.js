import React from 'react';
import ReactDOM from 'react-dom/client'; // Using React 18's createRoot API for better performance and concurrent features

// Import the main application component that orchestrates the dashboard
import App from './App';

// Import global styles for the application.
// This ensures that base styles, typography, and layout rules are applied across all components.
import './styles/main.css';

/**
 * The entry point for the React application.
 * This file is responsible for rendering the root React component (`App`) into the DOM.
 */

// Find the root DOM element where the React application will be mounted.
// This element is typically defined in `public/index.html` with an ID of 'root'.
const rootElement = document.getElementById('root');

// Check if the root element exists to prevent potential runtime errors
// if the HTML structure is not as expected.
if (rootElement) {
  // Create a React root using ReactDOM.createRoot.
  // This is the recommended way to render applications in React 18 and later,
  // enabling concurrent features and improved performance.
  const root = ReactDOM.createRoot(rootElement);

  // Render the main App component into the root.
  // React.StrictMode is a development tool that helps identify potential problems
  // in an application. It activates additional checks and warnings for its descendants.
  // It does not render any visible UI.
  root.render(
    <React.StrictMode>
      <App />
    </React.StrictMode>
  );
} else {
  // Log an error if the root element is not found. This indicates a critical issue
  // with the `public/index.html` file or the script loading order.
  console.error('Root element with ID "root" not found in the document. Cannot mount React application.');
}

// --- Optional: Service Worker and Web Vitals (for PWA and performance monitoring) ---
// If this application were a Progressive Web App (PWA), service workers would be registered here
// to enable offline capabilities and faster loading.
// import * as serviceWorkerRegistration from './serviceWorkerRegistration';
// serviceWorkerRegistration.unregister(); // Or .register() if enabling PWA features.

// If performance monitoring is desired, Web Vitals reporting would be configured here.
// import reportWebVitals from './reportWebVitals';
// reportWebVitals(); // Example: reportWebVitals(console.log);