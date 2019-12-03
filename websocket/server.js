const http = require('http');
const url = require('url');
const WebSocketServerPort = 4567;
const WebSocket = require('ws');
let server = http.createServer((request, response) => {
    /**
     * Receives POST parameter for notification
     */
    const getPostParam = (request, callback) => {
		const querystring = require('querystring')
        if (request.method === "POST") {
            let body = '';

            request.on('data', (data) => {
	            body += data;
	            if (body.length > 1e6) {
	                request.connection.destroy();
                }
	        });

	        request.on('end', () => {
				const POST = querystring.parse(body);
	            callback(POST);
	        });
        }
    }

    if (request.method === 'POST') {
        getPostParam(request, (POST) => {
			try {
                console.log(POST);
				notifyUser(JSON.parse(POST.data));
				response.writeHead(200);
			} catch (e) {
                console.error(e);
				response.writeHead(500);
			}
			response.end();
        })
        return;
    }
});

let notifClients = {};
let chatClients = {};
global.clients = {}; // store the connections

const wssNotif = new WebSocket.Server({ noServer: true });
const wssChat = new WebSocket.Server({ noServer: true });

wssNotif.on('connection', ws => {
})

wssChat.on('connection', (ws, request) => {
    console.log(request);
    const { query: { id }} = url.parse(request.resource, true);
    console.log(id);
})

server.on('upgrade', function upgrade(request, socket, head) {
    const pathname = url.parse(request.url).pathname;

    if (pathname === '/notif') {
        wssNotif.handleUpgrade(request, socket, head, function done(ws) {
            wssNotif.emit('connection', ws, request);
        });
    } else if (pathname === '/chat') {
        wssChat.handleUpgrade(request, socket, head, function done(ws) {
            wssChat.emit('connection', ws, request);
        });
    } else {
        socket.destroy();
    }
});


server.listen(WebSocketServerPort, () => {
    console.log('Server is listening')
})
.on('error', (err) => {
    if (err.code === 'EADDRINUSE') console.log('Port is already in use.')
});

// var websocketServer = new WebSocketServer({
//     httpServer: server
// });

// const websocketRequest = request => {
//     // start the connection
//     try {
//         const { query: { id }} = url.parse(request.resource, true);
//         let connection = request.accept(null, request.origin);
//         console.log(`New Connection ${id}`)
//         // save the connection for future reference
//         clients[Number(id)] = connection;

//         connection.on('close', function(reasonCode, description) {
//             console.log((new Date()) + ' Peer ' + connection.remoteAddress + ' disconnected.');
//         });
//     } catch (e) {
//         console.log('Unable to start a connection');
//         console.error(e);
//     }
// }

// websocketServer.on("request", websocketRequest);

const notifyUser = (data) => {
    console.log(data.receiverId);
	if (clients[Number(data.receiverId)]) {
		clients[Number(data.receiverId)].sendUTF(JSON.stringify(data))
	}
}

const messageUser = (data) => {
    const receiverId = Number(data.receiverId);
    if (clients[receiverId]) {
        clients[receiverId].sendUTF(data);
    }
}
