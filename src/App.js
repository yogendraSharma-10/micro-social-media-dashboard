import React, { useState, useEffect, createContext } from 'react';
import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';

// Core application components
import PostFeed from './components/posts/PostFeed';
import UserProfile from './components/users/UserProfile';
// NotificationBell is typically part of the Header, but a dedicated page for notifications is also common.
import NotificationPage from './components/notifications/NotificationPage'; // Assuming this component exists or will be created

// Auth components (assuming these exist in an 'auth' subfolder for better organization)
import Login from './components/auth/Login'; // Assuming this component exists or will be created
import Register from './components/auth/Register'; // Assuming this component