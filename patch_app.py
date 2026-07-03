import re

file_path = "/Users/elrya/fullstack_siapman/siapman_baru /lib/pages/camera_presensi_page.dart"
with open(file_path, "r") as f:
    content = f.read()

# 1. Remove ML Kit imports
content = re.sub(r"import '../services/face_detection_service\.dart';\n", "", content)
content = re.sub(r"import 'package:google_mlkit_face_detection/google_mlkit_face_detection\.dart';\n", "", content)

# 2. Remove state variables
content = re.sub(r"  int _currentStep = 0;\n", "", content)
content = re.sub(r"  String\? _activeSessionId;\n", "", content)
content = re.sub(r"  String\? _activeLivenessToken;\n", "", content)
content = re.sub(r"  final FaceDetectionService _faceDetectionService = FaceDetectionService\(\);\n", "", content)
content = re.sub(r"  bool _isProcessingFrame = false;\n", "", content)
content = re.sub(r"  bool _eyesWereClosed = false;\n", "", content)
content = re.sub(r"  bool _headWasTurned = false;\n", "", content)
content = re.sub(r"  double _stableTime = 0\.0;\n", "", content)
content = re.sub(r"  DateTime\? _lastFrameTime;\n", "", content)

# 3. Rewrite _startVerification
start_verif_new = """  Future<void> _startVerification() async {
    setState(() {
      _showInstructions = false;
      _statusText = 'Menyiapkan kamera HP...';
      _backendStatus = 'Siap';
      _backendElapsed = 0.0;
      _backendRequired = 10.0;
    });

    try {
      await _initCamera();

      if (!mounted || _isPageClosing) return;

      setState(() {
        _statusText = 'Arahkan wajah ke kamera dan klik tombol foto';
      });
    } catch (e) {
      if (!mounted || _isPageClosing) return;
      setState(() {
        _statusText = 'Gagal memulai verifikasi: $e';
      });
    }
  }"""
content = re.sub(r"  Future<void> _startVerification\(\) async \{.*?(?=  void _startImageStream\(\))", start_verif_new + "\n\n", content, flags=re.DOTALL)

# 4. Remove _startImageStream, _getRotation, _processFrame
content = re.sub(r"  void _startImageStream\(\) \{.*?(?=  void _refreshAttendanceMode\(\))", "", content, flags=re.DOTALL)

# 5. Rewrite _restartLivenessSession
restart_new = """  Future<void> _restartLivenessSession() async {
    if (_isPageClosing) return;

    setState(() {
      _statusText = 'Arahkan wajah ke kamera dan klik tombol foto';
    });
  }"""
content = re.sub(r"  Future<void> _restartLivenessSession\(\) async \{.*?(?=  Future<void> _captureAndSubmit\(\) async \{)", restart_new + "\n\n", content, flags=re.DOTALL)

# 6. Fix _captureAndSubmit parameters
content = re.sub(r"sessionId: _activeSessionId \?\? '',", "sessionId: '',", content)
content = re.sub(r"livenessToken: _activeLivenessToken \?\? '',", "livenessToken: '',", content)

# 7. Modify build method to remove step indicator and add capture button
# Remove step indicator and animated container
content = re.sub(r"                    _buildStepIndicator\(\),\n.*?                    const SizedBox\(height: 32\),\n", "", content, flags=re.DOTALL)

# Replace the camera circle part to add a button after it
camera_part = """                    _buildCameraCircle(),
                    const SizedBox(height: 20),
                    if (!_isProcessingAttendance && !_attendanceSent && _attendanceMode != AttendanceMode.outsideHours)
                      ElevatedButton.icon(
                        onPressed: _captureAndSubmit,
                        icon: const Icon(Icons.camera_alt),
                        label: const Text('Ambil Foto'),
                        style: ElevatedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 32),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                      ),
                    const SizedBox(height: 20),"""
content = re.sub(r"                    _buildCameraCircle\(\),\n                    const SizedBox\(height: 40\),", camera_part, content)

# 8. Remove unused UI methods: _getStepColor, _getStepInstruction, _buildStepIndicator, _buildStepPoint, _buildStepLine
content = re.sub(r"  Color _getStepColor\(bool isDark\) \{.*?(?=  Widget _buildCameraCircle\(\) \{)", "", content, flags=re.DOTALL)

with open(file_path, "w") as f:
    f.write(content)

print("Patch applied to camera_presensi_page.dart")
