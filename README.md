# Microblog 3 (LaCosina)

## Installation

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `composer install`.
3. Client `npm run dev`.
3. Client for Production `npm run build`.
___
## What's New

1. UI
    - Create Posts now becomes focused (Modal-like)
    - HTTP Request Polling and pagination for 'People you may know'
    - Improvements

2. Code
    - React Code Splitting (React.Lazy)
        - for better initial loading
    - React Redux
        - to handle global states
    - Cake 2 to Cake 3

___
## Dependencies and Libraries

1. Composer
    - JWT
    - Soft Deletable

2. Node
    - WebSocket
    - React
    - Redux

___
## API

Prefix: `/api`

1. Auth - `/auth`
    - `/login` POST - PUBLIC
        - Logs in the user and returns a JWT Token on success
        - Accepts
        ```
        username, password
        ```
        - `RETURN` - JWT Token
    - `/register` POST - PUBLIC
        - Creates a user entity and sends an activation link on success
        - Accepts
        ```
        username, first_name, last_name, email, birthdate, sex, password, confirm_password
        ```
        - `RETURN` - Status 201
    - `/activate/:key` GET - PUBLIC
        - Activates the user that matches the passed key
        - `RETURN` Redirect to /login
    - `/me` GET - PRIVATE
        - Fetches the user currently logged in via the JWT
        - `RETURN` - User Entity

2. Users - `/users`
    - `/` PUT - PRIVATE
        - Updates the current user profile
        - `RETURN` - User Entity

    - `/:id/followers` GET - PRIVATE
        - Fetches the followers of the given user
        - `RETURN` Array: User Entity

    - `/:id/following` GET - PRIVATE
        - Fetches the users being followed by the given user
        - `RETURN` Array: User Entity

    - `/:id/follow` POST - PRIVATE
        - Follows the given user
        - `RETURN` Object
            - Number of users being followed by the given user
            - Number of followers by the given user

    - `/:username/followers/count` GET - PRIVATE
        - Fetches the number of followers based on the given user
        - `RETURN` Integer - Number of followers

    - `/:username/following/count` GET - PRIVATE
        - Fetches the number of users being followed by the given user
        - `RETURN` Integer - Number of users being followed

    - `/:username/follow/count` GET - PRIVATE
        - Fetches both followers and following of the current user logged in
        - `RETURN` Object
            - Number of users being followed
            - Number of followers

    - `/:username` GET - PRIVATE
        - Fetches the profile by the given username
        - `RETURN` - User Entity

    - `/:username/mutual` GET - PRIVATE
        - Fetches followed users who followed the given user
        - `RETURN` - Array: User Entity

    - `/:username/is-following` GET - PRIVATE
        - Checks if username passed is being followed by the current user logged in
        - `RETURN` - 1 if is followed, 0 if not

    - `/follow/recommended` GET - PRIVATE
        - Fetches recommended users of the current user logged in
        - Prioritizes users who has been followed by your followed users
        - All users returned is not yet followed
        - `RETURN` Array: User Entity

    - `/update-image` POST - PRIVATE
        - Updates the profile picture of the current user logged in
        - `RETURN` 201 Created
    
3. Posts - `/posts`
    - `/` GET - PRIVATE
        - Fetches the posts to display in the landing page
        - `RETURN` Array: Post Entity

    - `/` - POST - PRIVATE
        - Adds a Post Entity
        - Accepts
        ```
        title, body: required, img
        ```
        - `RETURN` Post Entity

    - `/users/:username` GET - PRIVATE
        - Fetches the posts of the given user
        - `RETURN` Array: Post Entity
    
    - `/update/:id` POST - PRIVATE
        - Updates a Post Entity
        - Accepts
        ```
        title, body: required, img
        ```
        - `RETURN` Post Entity
    
    - `/like/:id` POST - PRIVATE
        - Toggles the like of the given post
        - `RETURN` Integer - Total Like of the given post

    - `/:id` GET - PRIVATE
        - Fetches a single post
        - :id = posts.id
        - `RETURN` Post Entity

    - `/:id` DELETE - PRIVATE
        - Deletes a single post
        - Permission
            - can only delete owned post
        - :id = posts.id
        - `RETURN` 204

    - `/:id/comments` GET - PRIVATE
        - Fetches comments of the given post
        - Paginated
        - `RETURN` Object
            - Array: Comment Entity
            - Integer: Total count of comments of the given post

    - `/:id/comments` POST - PRIVATE
        - Adds a comment to the given post
        - `RETURN` Integer - Total count of comments of the given post
    
    - `/:id/likers` GET - PRIVATE
        - Fetches users who liked the given post
        - :id = posts.id
        - `RETURN` Array: User Entity
    
    - `/comments/:id` DELETE - PRIVATE
        - Deletes a comment
        - Permission
            - can only delete owned comments
        - :id = comments.id
    
    - `/share/:id` POST - PRIVATE
        - Shares a post
        - :id = posts.id
        - `RETURN` Post Entity

4. Search - `/search`
    - `/` GET - PRIVATE
        - Searches for posts and users
        - Query Params
            - page: int - page number
            - text: string - the text to be used in search
        - `RETURN` Object
            - Array: User Entity
            - Array: Post Entity

    - `/users` GET - PRIVATE
        - Searches users
        - Query Params
            - page: int - page number
            - text: string - the text to be used in search
        - `RETURN` Array: User Entity

    - `/posts` GET - PRIVATE
        - Searches posts
        - Query Params
            - page: int - page number
            - text: string - the text to be used in search
        - `RETURN` Array: Post Entity
    
5. Notification - `/notifications`
    - `/notifications/unread` GET - PRIVATE
        - Fetches unread notifications of the current user
        - `RETURN` Array: Notification Entity

    - `/notifications/unread/count` GET - PRIVATE
        - Fetches total count of unread notifications of the current user
        - `RETURN` Integer

    - `/notifications/read` POST - PRIVATE
        - Sets all notifications of current user as read
        - `RETURN` 200

    - `/notifications/read/:id` POST - PRIVATE
        - Sets a notification as read
        - Permission
            - can only read owned notification
        - `RETURN` 200
