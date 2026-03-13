import React, { useState, useEffect, useCallback } from 'react';
import { useParams } from 'react-router-dom';
import * as socialApi from '../../api/socialApi';
import PostFeed from '../posts/PostFeed';
import '../../styles/main.css'; // Assuming main.css contains styles for user profiles

/**
 * UserProfile Component
 *
 * Displays a user's profile, including their details, follower/following counts,
 * a follow/unfollow button, and a feed of their posts.
 *
 * Fetches user data, their posts, and the current user's follow status
 * for the displayed profile.
 */
const UserProfile = () => {
    // Get the userId from the URL parameters
    const { userId } = useParams();

    // State for user data, posts, loading status, and error messages
    const [user, setUser] = useState(null);
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [isFollowing, setIsFollowing] = useState(false);
    const [followerCount, setFollowerCount] = useState(0);

    // Placeholder for the currently logged-in user's ID (would come from auth context)
    // For demonstration, let's assume a static ID or fetch it from a global state/context.
    // In a real app, this would be dynamic, e.g., from `AuthContext`.
    const currentLoggedInUserId = 1; // Example: User with ID 1 is logged in

    /**
     * Fetches user data, their posts, and follow status.
     * Uses useCallback to memoize the function and prevent unnecessary re-renders
     * if passed down to children, though not strictly necessary here.
     */
    const fetchUserProfileData = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            // Fetch user details
            const userData = await socialApi.getUserProfile(userId);
            setUser(userData);
            setFollowerCount(userData.followers_count || 0);

            // Fetch posts by this user
            const userPosts = await socialApi.getUserPosts(userId);
            setPosts(userPosts);

            // Check if the current logged-in user is following this profile
            // Only if the profile being viewed is not the logged-in user's own profile
            if (parseInt(userId) !== currentLoggedInUserId) {
                const followStatus = await socialApi.checkFollowStatus(currentLoggedInUserId, userId);
                setIsFollowing(followStatus.isFollowing);
            } else {
                setIsFollowing(false); // Cannot follow/unfollow self
            }

        } catch (err) {
            console.error("Failed to fetch user profile:", err);
            setError("Failed to load user profile. Please try again.");
        } finally {
            setLoading(false);
        }
    }, [userId, currentLoggedInUserId]); // Dependencies for useCallback

    // Effect hook to fetch data when the component mounts or userId changes
    useEffect(() => {
        fetchUserProfileData();
    }, [fetchUserProfileData]); // Dependency array includes the memoized fetch function

    /**
     * Handles the follow/unfollow toggle action.
     * Calls the appropriate API endpoint and updates the UI state.
     */
    const handleFollowToggle = async () => {
        if (!user || parseInt(userId) === currentLoggedInUserId) return; // Prevent following self or if user data isn't loaded

        try {
            if (isFollowing) {
                await socialApi.unfollowUser(currentLoggedInUserId, userId);
                setIsFollowing(false);
                setFollowerCount(prev => prev - 1);
            } else {
                await socialApi.followUser(currentLoggedInUserId, userId);
                setIsFollowing(true);
                setFollowerCount(prev => prev + 1);
            }
        } catch (err) {
            console.error("Failed to toggle follow status:", err);
            setError("Failed to update follow status. Please try again.");
        }
    };

    if (loading) {
        return <div className="container text-center mt-5">Loading profile...</div>;
    }

    if (error) {
        return <div className="container text-center mt-5 text-danger">{error}</div>;
    }

    if (!user) {
        return <div className="container text-center mt-5">User not found.</div>;
    }

    return (
        <div className="user-profile-page container mt-4">
            <div className="profile-header card p-4 mb-4 shadow-sm">
                <div className="d-flex align-items-center mb-3">
                    <img
                        src={user.profile_picture_url || 'https://via.placeholder.com/150'}
                        alt={`${user.username}'s profile`}
                        className="profile-avatar rounded-circle me-4"
                        style={{ width: '120px', height: '120px', objectFit: 'cover' }}
                    />
                    <div>
                        <h2 className="mb-1">{user.username}</h2>
                        <p className="text-muted">@{user.username}</p>
                        <p className="profile-bio">{user.bio || 'No bio available.'}</p>
                        <div className="profile-stats d-flex mt-2">
                            <span className="me-3"><strong>{posts.length}</strong> Posts</span>
                            <span className="me-3"><strong>{followerCount}</strong> Followers</span>
                            <span><strong>{user.following_count || 0}</strong> Following</span>
                        </div>
                    </div>
                </div>

                {/* Follow/Unfollow button, only if not viewing own profile */}
                {parseInt(userId) !== currentLoggedInUserId && (
                    <button
                        onClick={handleFollowToggle}
                        className={`btn ${isFollowing ? 'btn-outline-secondary' : 'btn-primary'} mt-3`}
                    >
                        {isFollowing ? 'Unfollow' : 'Follow'}
                    </button>
                )}

                {/* Potential integration point for other services */}
                {/* <div className="mt-4">
                    <p className="text-muted">
                        Check out {user.username}'s activity on the <a href={`/whiteboard/${userId}`}>Collaborative Whiteboard</a> or their listings on the <a href={`/marketplace/seller/${userId}`}>E-commerce Marketplace</a>.
                    </p>
                </div> */}
            </div>

            <div className="profile-posts">
                <h3 className="mb-3">Posts by {user.username}</h3>
                {posts.length > 0 ? (
                    <PostFeed posts={posts} />
                ) : (
                    <p className="text-center text-muted">No posts yet.</p>
                )}
            </div>
        </div>
    );
};

export default UserProfile;