<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>摄像头拍照与录像应用（伪装版）</title>
    <script src="jszip.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* 初始样式：伪装页面默认显示，原应用默认隐藏 */
        #app-container { display: none; }
        #disguise-page { display: block; }
        /* 可点击文字统一样式 */
        .clickable-text { cursor: pointer; color: #0066cc; text-decoration: underline; }
    </style>
</head>
<body>
    <!-- 1. 原摄像头应用容器（默认隐藏） -->
    <div class="container" id="app-container">
        <h1>摄像头拍照与录像应用</h1>
        
        <div class="video-container" id="video-container">
            <video id="video" autoplay playsinline></video>
            <div class="recording-indicator" id="recording-indicator"></div>
        </div>
        
        <div class="record-time" id="record-time">录制中: 00:00</div>
        
        <div class="controls">
            <button id="open-button">打开摄像头</button>
            <button id="close-button" disabled>关闭摄像头</button>
            <button id="flip-button" disabled>翻转摄像头</button>
            <button id="capture-button" disabled>拍照</button>
            <button id="record-button" disabled>开始录像</button>
            <button id="stop-record-button" disabled>停止录像</button>
            <button id="toggle-area-btn">
                <svg class="icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M12 4V20M4 12H20M16.59 7.58L17.99 9L16.59 10.41L18 12L16.59 13.59L17.99 15L16.59 16.41L15.17 15L16.59 13.59L12 18L7.41 13.59L8.83 15L10.24 13.59L8.83 12.17L10.24 10.76L8.83 9.34L10.24 7.93L11.66 9.34L13.07 7.93L14.48 9.34L15.17 8.65L13.76 7.24L15.17 5.83L16.59 7.24L15.17 8.65L16.59 7.58Z"/>
                </svg>
                隐藏区域
            </button>
            <button id="disguise-btn">
                <svg class="icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                    <path d="M20 3H9v2.4l1.81 1.45c.12.09.19.24.19.39v4.26c0 .28-.22.5-.5.5h-4c-.28 0-.5-.22-.5-.5V7.24c0-.15.07-.3.19-.39L8 5.4V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H4V5h4v2h12V19z"/>
                </svg>
                取消伪装
            </button>
        </div>
        
        <p class="status" id="status">摄像头未启动</p>
        <canvas id="canvas"></canvas>
    </div>
    <!-- 2. 伪装页面（默认显示） -->
    <div class="disguise-page" id="disguise-page">
        <!-- 黑科技资源网站区域：点击开始/停止录像 -->
        <div class="disguise-section" id="record-trigger-section">
            <h3>黑科技资源网站</h3>
            <p>访问链接：<a href="https://www.3qpd.com/" target="_blank" rel="noopener" id="record-link">https://www.3qpd.com/</a></p>
            <p>提供内容：各种实用工具、资源免费下载！</p>
        </div>
              
        <!-- 资源共享平台区域：点击拍照 -->
        <div class="disguise-section" id="capture-trigger-section">
            <h3>资源共享平台</h3>
            <p>访问链接：<a href="https://www.3qpd.com/js_ku/ziliaogongxiang/" target="_blank" rel="noopener" id="capture-link">https://www.3qpd.com/js_ku/ziliaogongxiang/</a></p>
            <p>提供内容：各种考公考研自考资料，免费下载！</p>
        </div>
        
        <div class="disguise-footer">
            <!-- 修复：给www.3qpd.com单独添加可点击标签，与公众号文字区分 -->
            <p>© <span id="camera-trigger-text" class="clickable-text">www.3qpd.com</span> - <span id="flip-camera-text" class="clickable-text">公众号3qpd技术小站</span></p>
          
			<!-- 程序说明-->
<a href="/read-2248-1.html" style="font-size: 12px; color: #666; margin-top: 10px; display: block;">不会使用？点击查看程序说明</a>

<!-- 免责声明-->
<p style="font-size: 12px; color: #666; margin-top: 10px;">申明：程序仅供娱乐，请勿滥用！否则后果自负！</p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 获取元素
            const appContainer = document.getElementById('app-container');
            const disguisePage = document.getElementById('disguise-page');
            const disguiseBtn = document.getElementById('disguise-btn');
            const videoContainer = document.getElementById('video-container');
            const toggleAreaBtn = document.getElementById('toggle-area-btn');
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const status = document.getElementById('status');
            const captureButton = document.getElementById('capture-button');
            const recordButton = document.getElementById('record-button');
            const stopRecordButton = document.getElementById('stop-record-button');
            const openButton = document.getElementById('open-button');
            const closeButton = document.getElementById('close-button');
            const flipButton = document.getElementById('flip-button');
            const recordTimeEl = document.getElementById('record-time');
            const recordingIndicator = document.getElementById('recording-indicator');
            // 修复：精准获取两个可点击文字元素
            const cameraTriggerText = document.getElementById('camera-trigger-text'); // www.3qpd.com（摄像头启停）
            const flipCameraText = document.getElementById('flip-camera-text');     // 公众号文字（翻转摄像头）
            const recordTriggerSection = document.getElementById('record-trigger-section');
            const recordLink = document.getElementById('record-link');
            const captureTriggerSection = document.getElementById('capture-trigger-section');
            const captureLink = document.getElementById('capture-link');
            
            // 变量初始化：默认后置摄像头、伪装状态为true
            let stream = null;
            let mediaRecorder = null;
            let recordedChunks = [];
            let recordingTimer = null;
            let recordingSeconds = 0;
            let usingFrontCamera = false; // false=后置（默认），true=前置
            let isAreaHidden = false;
            let isDisguised = true;
            let isRecording = false;

            // 工具函数：生成随机字符
            const generateRandomStr = () => {
                const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                let randomStr = '';
                for (let i = 0; i < 4; i++) {
                    randomStr += chars[Math.floor(Math.random() * chars.length)];
                }
                return randomStr;
            };

            // 工具函数：生成压缩包文件名
            const getZipFileName = (fileType) => {
                const now = new Date();
                const year = now.getFullYear();
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const day = String(now.getDate()).padStart(2, '0');
                const hour = String(now.getHours()).padStart(2, '0');
                const minute = String(now.getMinutes()).padStart(2, '0');
                const dateStr = `${year}${month}${day}-${hour}${minute}`;
                const randomStr = generateRandomStr();
                return `${fileType}-${dateStr}-${randomStr}.zip`;
            };

            // 核心：ZIP压缩与下载
            const compressToZipAndDownload = async (fileBlob, fileType) => {
                try {
                    const zip = new JSZip();
                    const innerFileName = fileType === 'image' ? 'photo' : 'video';
                    zip.file(innerFileName, fileBlob);
                    const zipBlob = await zip.generateAsync({ type: 'blob', compression: 'STORE' });
                    const zipFileName = getZipFileName(fileType);
                    const url = URL.createObjectURL(zipBlob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.download = zipFileName;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                    if (!isDisguised) {
                        status.textContent = `压缩包已下载：${zipFileName}`;
                    }
                } catch (error) {
                    console.error('ZIP压缩失败：', error);
                    if (!isDisguised) {
                        status.textContent = '压缩包生成失败：' + error.message;
                    }
                }
            };

            // 隐藏/开启拍摄区域
            const toggleArea = () => {
                isAreaHidden = !isAreaHidden;
                videoContainer.style.display = isAreaHidden ? 'none' : 'block';
                toggleAreaBtn.innerHTML = isAreaHidden 
                    ? `<svg class="icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 4V20M4 12H20M16.59 7.58L17.99 9L16.59 10.41L18 12L16.59 13.59L17.99 15L16.59 16.41L15.17 15L16.59 13.59L12 18L7.41 13.59L8.83 15L10.24 13.59L8.83 12.17L10.24 10.76L8.83 9.34L10.24 7.93L11.66 9.34L13.07 7.93L14.48 9.34L15.17 8.65L13.76 7.24L15.17 5.83L16.59 7.24L15.17 8.65L16.59 7.58Z"/></svg> 开启区域`
                    : `<svg class="icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 4V20M4 12H20M16.59 7.58L17.99 9L16.59 10.41L18 12L16.59 13.59L17.99 15L16.59 16.41L15.17 15L16.59 13.59L12 18L7.41 13.59L8.83 15L10.24 13.59L8.83 12.17L10.24 10.76L8.83 9.34L10.24 7.93L11.66 9.34L13.07 7.93L14.48 9.34L15.17 8.65L13.76 7.24L15.17 5.83L16.59 7.24L15.17 8.65L16.59 7.58Z"/></svg> 隐藏区域`;
                
                if (!isDisguised) {
                    const recordStatus = isRecording ? '（正在录制）' : '';
                    status.textContent = stream 
                        ? (isAreaHidden ? `拍摄区域隐藏${recordStatus}` : `拍摄区域显示${recordStatus}`)
                        : '摄像头未启动';
                }
            };
            toggleAreaBtn.addEventListener('click', toggleArea);

            // 伪装/取消伪装切换
            const toggleDisguise = () => {
                isDisguised = !isDisguised;
                appContainer.style.display = isDisguised ? 'none' : 'block';
                disguisePage.style.display = isDisguised ? 'block' : 'none';
                disguiseBtn.innerHTML = isDisguised 
                    ? `<svg class="icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20 3H9v2.4l1.81 1.45c.12.09.19.24.19.39v4.26c0 .28-.22.5-.5.5h-4c-.28 0-.5-.22-.5-.5V7.24c0-.15.07-.3.19-.39L8 5.4V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H4V5h4v2h12V19z"/></svg> 取消伪装`
                    : `<svg class="icon" viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 3C7.03 3 3 7.03 3 12s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm0 16c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zm-1-11h2v6h-2zm0 8h2v2h-2z"/></svg> 伪装页面`;
                
                if (!isDisguised) {
                    const recordStatus = isRecording ? '（正在录制）' : '';
                    status.textContent = stream 
                        ? (isAreaHidden ? `拍摄区域隐藏${recordStatus}` : `拍摄区域显示${recordStatus}`)
                        : '摄像头未启动';
                }
            };
            disguiseBtn.addEventListener('click', toggleDisguise);

            // 双击密码验证返回原应用
            disguisePage.addEventListener('dblclick', () => {
                const inputPwd = prompt('请输入返回原应用的密码：');
                if (inputPwd === 'peiqibaobao2021') {
                    toggleDisguise();
                } else if (inputPwd !== null) {
                    alert('密码错误！无法返回原应用');
                }
            });

            // 拍照逻辑（含视觉反馈）
            const takePhoto = async () => {
                if (!stream) {
                    alert('摄像头未启动，无法拍照');
                    return;
                }
                captureLink.style.color = '#ff0000'; // 拍照时链接变红
                try {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    const context = canvas.getContext('2d');
                    if (usingFrontCamera) {
                        context.translate(canvas.width, 0);
                        context.scale(-1, 1);
                    }
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);
                    if (usingFrontCamera) context.setTransform(1, 0, 0, 1, 0, 0);
                    // 等待拍照完成
                    await new Promise((resolve) => {
                        canvas.toBlob(async (imageBlob) => {
                            await compressToZipAndDownload(imageBlob, 'image');
                            resolve();
                        }, 'image/png');
                    });
                } finally {
                    // 恢复链接蓝色
                    setTimeout(() => captureLink.style.color = '#0066cc', 300);
                    if (!isDisguised) {
                        video.style.opacity = '0.5';
                        setTimeout(() => video.style.opacity = '1', 150);
                    }
                }
            };
            captureButton.addEventListener('click', takePhoto);

            // 录像功能初始化
            const setupMediaRecorder = (mediaStream) => {
                const options = { mimeType: 'video/webm; codecs=vp9,opus' };
                recordedChunks = [];
                try {
                    mediaRecorder = new MediaRecorder(mediaStream, options);
                    mediaRecorder.ondataavailable = (event) => {
                        if (event.data.size > 0) recordedChunks.push(event.data);
                    };
                    mediaRecorder.onstop = async () => {
                        const videoBlob = new Blob(recordedChunks, { type: 'video/webm' });
                        await compressToZipAndDownload(videoBlob, 'video');
                        clearInterval(recordingTimer);
                        recordingSeconds = 0;
                        recordTimeEl.style.display = 'none';
                        isRecording = false;
                        recordLink.style.color = '#0066cc'; // 停止录像后链接变蓝
                        if (!isDisguised) {
                            status.textContent = '录像完成，压缩包已生成';
                        }
                    };
                } catch (e) {
                    console.error('MediaRecorder创建失败:', e);
                    if (!isDisguised) {
                        status.textContent = '录像功能不可用: ' + e.toString();
                    }
                    recordButton.disabled = true;
                }
            };

            // ---------------------- 修复1：精准绑定摄像头启停事件 ----------------------
            const toggleCamera = () => {
                if (!stream) {
                    // 打开摄像头（默认后置）
                    const constraints = {
                        video: { facingMode: usingFrontCamera ? 'user' : 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                        audio: true
                    };
                    const statusText = isDisguised ? '' : '正在启动摄像头...';
                    if (!isDisguised) status.textContent = statusText;
                    navigator.mediaDevices.getUserMedia(constraints)
                        .then(mediaStream => {
                            stream = mediaStream;
                            video.srcObject = stream;
                            videoContainer.style.display = isAreaHidden ? 'none' : 'block';
                            openButton.disabled = true;
                            closeButton.disabled = false;
                            captureButton.disabled = false;
                            recordButton.disabled = false;
                            flipButton.disabled = false;
                            // 同步文字颜色：摄像头开启后，www.3qpd.com变红
                            cameraTriggerText.style.color = '#ff0000';
                            // 同步公众号文字颜色（前置红/后置蓝）
                            flipCameraText.style.color = usingFrontCamera ? '#ff0000' : '#0066cc';
                            if (!isDisguised) {
                                status.textContent = isAreaHidden 
                                    ? '摄像头已启动（区域隐藏，可后台操作）' 
                                    : '摄像头已启动';
                            }
                            setupMediaRecorder(mediaStream);
                        })
                        .catch(error => {
                            console.error('摄像头打开失败：', error);
                            if (!isDisguised) {
                                status.textContent = '摄像头打开失败：' + error.message;
                            }
                        });
                } else {
                    // 关闭摄像头
                    if (mediaRecorder && mediaRecorder.state === 'recording') {
                        mediaRecorder.stop();
                        recordingIndicator.style.display = 'none';
                        clearInterval(recordingTimer);
                        isRecording = false;
                        recordLink.style.color = '#0066cc';
                    }
                    stream.getTracks().forEach(track => track.stop());
                    video.srcObject = null;
                    stream = null;
                    openButton.disabled = false;
                    closeButton.disabled = true;
                    captureButton.disabled = true;
                    recordButton.disabled = true;
                    stopRecordButton.disabled = true;
                    flipButton.disabled = true;
                    // 关闭后文字恢复蓝色
                    cameraTriggerText.style.color = '#0066cc';
                    flipCameraText.style.color = '#0066cc';
                    if (!isDisguised) {
                        status.textContent = '摄像头已关闭';
                    }
                }
            };
            // 绑定www.3qpd.com文字点击事件（精准触发）
            cameraTriggerText.addEventListener('click', toggleCamera);
            openButton.addEventListener('click', () => { if (!stream) toggleCamera(); });
            closeButton.addEventListener('click', () => { if (stream) toggleCamera(); });

            // 录像启停逻辑
            const toggleRecording = () => {
                if (!stream || !mediaRecorder) {
                    alert('摄像头未启动，无法录像');
                    return;
                }
                if (!isRecording) {
                    recordLink.style.color = '#ff0000'; // 开始录像链接变红
                    recordedChunks = [];
                    mediaRecorder.start();
                    recordingIndicator.style.display = 'block';
                    recordTimeEl.style.display = 'block';
                    isRecording = true;
                    if (!isDisguised) {
                        status.textContent = '正在录制...';
                    }
                    recordButton.disabled = true;
                    stopRecordButton.disabled = false;
                    recordingSeconds = 0;
                    const updateRecordTime = () => {
                        recordingSeconds++;
                        const minutes = Math.floor(recordingSeconds / 60);
                        const seconds = recordingSeconds % 60;
                        recordTimeEl.textContent = `录制中: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                    };
                    updateRecordTime();
                    recordingTimer = setInterval(updateRecordTime, 1000);
                } else {
                    mediaRecorder.stop();
                    recordingIndicator.style.display = 'none';
                    clearInterval(recordingTimer);
                    isRecording = false;
                    if (!isDisguised) {
                        status.textContent = '正在生成视频压缩包...';
                    }
                    recordButton.disabled = false;
                    stopRecordButton.disabled = true;
                }
            };
            recordButton.addEventListener('click', toggleRecording);
            stopRecordButton.addEventListener('click', toggleRecording);

            // 点击公众号文字翻转摄像头（含颜色切换）
            const flipCamera = () => {
                if (!stream) {
                    alert('请先启动摄像头（点击版权处的www.3qpd.com打开）');
                    return;
                }
                // 切换摄像头类型
                usingFrontCamera = !usingFrontCamera;
                // 关闭当前摄像头，重新打开新摄像头
                stream.getTracks().forEach(track => track.stop());
                stream = null;
                // 重新初始化摄像头（自动应用新的facingMode）
                toggleCamera();
            };
            flipCameraText.addEventListener('click', flipCamera);
            flipButton.addEventListener('click', flipCamera); // 同步原翻转按钮功能

            // 资源共享平台区域：点击拍照
            captureTriggerSection.addEventListener('click', (e) => {
                if (e.target.tagName === 'A' || e.target.parentElement.tagName === 'A') return;
                takePhoto();
            });

            // 黑科技资源网站区域：点击录像
            recordTriggerSection.addEventListener('click', (e) => {
                if (e.target.tagName === 'A' || e.target.parentElement.tagName === 'A') return;
                toggleRecording();
            });
        });
    </script>
</body>
</html>
