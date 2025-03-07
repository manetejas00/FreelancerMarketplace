// utils/encryption.js

export function encryptData(data) {
    return btoa(data); // Base64 encode for safe URL usage
}

export function decryptData(encodedData) {
    return atob(encodedData); // Base64 decode
}
