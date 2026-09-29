/**
 * S-DEGAN Sound & Notification Engine
 * - Web Audio API synthesizer for instant, reliable chime playback (no external assets required).
 * - Real-time lightweight polling for Operator submission & Verifikator validation events.
 * - SweetAlert2 / floating toast notifications.
 */

(function () {
    'use strict';

    let audioCtx = null;
    let isAudioUnlocked = false;

    // Inisialisasi Audio Context
    function getAudioContext() {
        if (!audioCtx) {
            const AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                audioCtx = new AudioContextClass();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume();
        }
        return audioCtx;
    }

    // Buka proteksi browser Autoplay Policy pada interaksi pertama user
    function unlockAudio() {
        if (isAudioUnlocked) return;
        const ctx = getAudioContext();
        if (ctx) {
            if (ctx.state === 'suspended') {
                ctx.resume().then(() => {
                    isAudioUnlocked = true;
                });
            } else {
                isAudioUnlocked = true;
            }
        }
    }

    ['click', 'touchstart', 'keydown'].forEach(evt => {
        document.addEventListener(evt, unlockAudio, { once: true, passive: true });
    });

    /**
     * Memainkan suara lonceng / chime yang jernih dan merdu
     */
    function playNotificationSound(type = 'chime') {
        try {
            const ctx = getAudioContext();
            if (!ctx) return;

            const now = ctx.currentTime;

            if (type === 'success') {
                // Suara sukses ceria (Nada C5 -> E5 -> G5)
                [523.25, 659.25, 783.99].forEach((freq, idx) => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + idx * 0.1);
                    
                    gain.gain.setValueAtTime(0, now + idx * 0.1);
                    gain.gain.linearRampToValueAtTime(0.3, now + idx * 0.1 + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + idx * 0.1 + 0.35);
                    
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    
                    osc.start(now + idx * 0.1);
                    osc.stop(now + idx * 0.1 + 0.36);
                });
            } else {
                // Suara notifikasi chime elegan (D5 -> A5)
                const notes = [
                    { freq: 587.33, start: 0, duration: 0.25 },  // D5
                    { freq: 880.00, start: 0.12, duration: 0.55 } // A5
                ];

                notes.forEach(note => {
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();

                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(note.freq, now + note.start);

                    gain.gain.setValueAtTime(0, now + note.start);
                    gain.gain.linearRampToValueAtTime(0.35, now + note.start + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, now + note.start + note.duration);

                    osc.connect(gain);
                    gain.connect(ctx.destination);

                    osc.start(now + note.start);
                    osc.stop(now + note.start + note.duration + 0.01);
                });
            }
        } catch (e) {
            console.warn('[S-DEGAN Notif] Gagal memutar audio:', e);
        }
    }

    /**
     * Tampilkan toast notifikasi visual
     */
    function showNotificationToast(title, message, targetUrl) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '🔔 ' + title,
                text: message,
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: !!targetUrl,
                confirmButtonText: 'Buka Sekarang',
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                timer: 12000,
                timerProgressBar: true,
                customClass: {
                    popup: 'sdegan-toast-popup'
                }
            }).then((result) => {
                if (result.isConfirmed && targetUrl) {
                    window.location.href = targetUrl;
                }
            });
        } else {
            // Fallback jika SweetAlert tidak ter-load
            alert(title + '\n\n' + message);
        }
    }

    /**
     * Engine Polling Real-time
     */
    function initPolling() {
        const checkUrl = window.SDeganNotificationConfig?.checkUrl || '/notifications/check';
        const userRole = window.SDeganNotificationConfig?.userRole || '';

        // Kunci storage agar state tersimpan per user role
        const storageKey = 'sdegan_last_notif_' + (userRole || 'guest');
        let pollTimer = null;
        let isInitialCheck = true;

        async function poll() {
            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 6000);

                const response = await fetch(checkUrl, {
                    signal: controller.signal,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                clearTimeout(timeoutId);

                if (response.status === 401 || response.status === 419) {
                    if (pollTimer) clearInterval(pollTimer);
                    return;
                }

                if (!response.ok) return;
                const data = await response.json();

                if (!data || !data.has_notification) {
                    return;
                }

                // Buat unique signature dari data
                const currentSignature = (data.role || '') + '_' + 
                                         (data.latest_id || '') + '_' + 
                                         (data.latest_time || '') + '_' + 
                                         (data.latest_status || '') + '_' + 
                                         (data.pending_count || '');

                const savedSignature = localStorage.getItem(storageKey);

                if (isInitialCheck) {
                    // Pada saat pertama buka halaman, simpan signature awal
                    if (!savedSignature) {
                        localStorage.setItem(storageKey, currentSignature);
                    }
                    isInitialCheck = false;
                    return;
                }

                // Jika ada perubahan signature
                if (savedSignature !== currentSignature) {
                    localStorage.setItem(storageKey, currentSignature);
                    
                    // Mainkan suara chime
                    playNotificationSound('chime');

                    // Tampilkan notifikasi visual
                    showNotificationToast(
                        data.title || 'Pemberitahuan Baru',
                        data.message || 'Terdapat pembaruan data pangan.',
                        data.target_url || null
                    );
                }
            } catch (err) {
                // Abaikan error jaringan / abort
            }
        }

        // Jalankan polling pertama setelah 3 detik, lalu setiap 10 detik
        setTimeout(poll, 3000);
        pollTimer = setInterval(poll, 10000);
    }

    // Mainkan suara jika ada flash message sukses di session
    function checkFlashSuccess() {
        if (window.SDeganFlashSuccess) {
            setTimeout(() => {
                playNotificationSound('success');
            }, 300);
        }
    }

    // Expose ke global window
    window.SDeganSound = {
        play: (type) => playNotificationSound(type),
        notify: (title, message, url) => {
            playNotificationSound('chime');
            showNotificationToast(title, message, url);
        }
    };

    // Jalankan saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            checkFlashSuccess();
            initPolling();
        });
    } else {
        checkFlashSuccess();
        initPolling();
    }
})();
