const http = require('http');
const url = require('url');
const queryString = require('query-string');
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
const wssChat = new WebSocket.Server({
    noServer: true,
    perMessageDeflate: {
        zlibDeflateOptions: {
        // See zlib defaults.
        chunkSize: 1024,
        memLevel: 7,
        level: 3
        },
        zlibInflateOptions: {
        chunkSize: 10 * 1024
        },
        // Other options settable:
        clientNoContextTakeover: true, // Defaults to negotiated value.
        serverNoContextTakeover: true, // Defaults to negotiated value.
        serverMaxWindowBits: 10, // Defaults to negotiated value.
        // Below options specified as default values.
        concurrencyLimit: 10, // Limits zlib concurrency for perf.
        threshold: 1024 // Size (in bytes) below which messages
        // should not be compressed.
    }
});

wssNotif.on('connection', (ws, request, { id }) => {
    notifClients[Number(id)] = ws;
})

wssChat.on('connection', (ws, request, { id }) => {
    const receiver_id = Number(id);
    chatClients[receiver_id] = ws;
    ws.on('message', data => {
        messageUser(JSON.parse(data));
    })
    ws.on('close', () => {
        delete chatClients.receiver_id;
    })
    ws.on('error', () => {
        delete chatClients.receiver_id;
    })
})

server.on('upgrade', function upgrade(request, socket, head) {
    const pathname = url.parse(request.url).pathname;
    const { query } = queryString.parseUrl(request.url);

    if (pathname === '/') {
        wssNotif.handleUpgrade(request, socket, head, function done(ws) {
            wssNotif.emit('connection', ws, request, query);
        });
    } else if (pathname === '/chat') {
        wssChat.handleUpgrade(request, socket, head, function done(ws) {
            wssChat.emit('connection', ws, request, query);
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
	if (notifClients[Number(data.receiverId)]) {
		notifClients[Number(data.receiverId)].send(JSON.stringify(data))
	}
}

const messageUser = (data) => {
    const receiver_id = Number(data.receiver_id);
    if ( ! chatClients[receiver_id]) return;

    if (chatClients[receiver_id].readyState !== chatClients[receiver_id].OPEN) {
        delete chatClients.receiver_id;
        return;
    }
    chatClients[receiver_id].send(JSON.stringify(data));
}
