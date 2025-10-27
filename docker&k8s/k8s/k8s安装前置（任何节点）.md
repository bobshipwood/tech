[toc]

### 1 Verify the MAC address and product_uuid are unique for every node

ip link or ifconfig -a for mac address
sudo cat /sys/class/dmi/id/product_uuid for product_uuid

### 2 Check required ports 

nc 127.0.0.1 6443 -v (这个端口在kubeadm init后才有，为控制中心的server

Control plane

| Protocol | Direction | Port Range | Purpose                 | Used By              |
| -------- | --------- | ---------- | ----------------------- | -------------------- |
| TCP      | Inbound   | 6443       | Kubernetes API server   | All                  |
| TCP      | Inbound   | 2379-2380  | etcd server client API  | kube-apiserver, etcd |
| TCP      | Inbound   | 10250      | Kubelet API             | Self, Control plane  |
| TCP      | Inbound   | 10259      | kube-scheduler          | Self                 |
| TCP      | Inbound   | 10257      | kube-controller-manager | Self                 |

Worker node(s)

| Protocol | Direction | Port Range  | Purpose            | Used By              |
| -------- | --------- | ----------- | ------------------ | -------------------- |
| TCP      | Inbound   | 10250       | Kubelet API        | Self, Control plane  |
| TCP      | Inbound   | 10256       | kube-proxy         | Self, Load balancers |
| TCP      | Inbound   | 30000-32767 | NodePort Services† | All                  |

### 3 Swap configuration

The default behavior of a kubelet is to fail to start if swap memory is detected on a node. This means that swap should either be disabled or tolerated by kubelet.

- To tolerate swap, add `failSwapOn: false` to kubelet configuration or as a command line argument. Note: even if `failSwapOn: false` is provided, workloads wouldn't have swap access by default. This can be changed by setting a `swapBehavior`, again in the kubelet configuration file. To use swap, set a `swapBehavior` other than the default `NoSwap` setting. See [Swap memory management](https://kubernetes.io/docs/concepts/architecture/nodes/#swap-memory) for more details.
- To disable swap, `sudo swapoff -a` can be used to disable swapping temporarily. To make this change persistent across reboots, make sure swap is disabled in config files like `/etc/fstab`, `systemd.swap`, depending how it was configured on your system

### 3.5 更改docker的cgroup为systemd，因为高版本的k8s默认是systemd，及（registry-mirrors大概率不行，需要科学上网）

vim /etc/docker/daemon.json

```
{
  "exec-opts": ["native.cgroupdriver=systemd"],
  "registry-mirrors": [
    "https://registry.cn-hangzhou.aliyuncs.com/"
  ]
}
```

### 3.8 安装docker 

```
sudo apt-get install docker.io
sudo apt install docker.io
```



### 4 更改主机名 （设置节点名字）

sudo hostnamectl set-hostname new-hostname
bash 或者 exec sudo -i

### 5 安装cri-dockerd（docker的运行时）并修改国内镜像地址(控制和节点都需安装)

下载并安装cri-dockerd_0.3.14.3-0.ubuntu-bionic_amd64.deb

sudo dpkg -i ./cri-dockerd_0.3.14.3-0.ubuntu-bionic_amd64.deb

sudo vim /lib/systemd/system/cri-docker.service

在service的execStart这一行的最后，加入 （--pod-infra-container-image=registry.cn-hangzhou.aliyuncs.com/google_containers/pause:3.9）
ExecStart=/usr/bin/cri-dockerd --container-runtime-endpoint fd:// --pod-infra-container-image=registry.cn-hangzhou.aliyuncs.com/google_containers/pause:3.9

sudo systemctl daemon-reload

sudo systemctl enable cri-docker  && sudo systemctl restart cri-docker

### 6 kubeadm 安装

1. Update the `apt` package index and install packages needed to use the Kubernetes `apt` repository:

   ```shell
   sudo apt-get update
   # apt-transport-https may be a dummy package; if so, you can skip that package
   sudo apt-get install -y apt-transport-https ca-certificates curl gpg
   ```

2. Download the public signing key for the Kubernetes package repositories. The same signing key is used for all repositories so you can disregard the version in the URL:（这一步因为防火墙的缘故，所以提前下载好Release.key文件，在目录k8s仓库证书v1.29下有）(命令将改为cat Release.key| sudo gpg --dearmor -o /etc/apt/keyrings/kubernetes-apt-keyring.gpg)

   ```shell
   # If the directory `/etc/apt/keyrings` does not exist, it should be created before the curl command, read the note below.
   # sudo mkdir -p -m 755 /etc/apt/keyrings
   curl -fsSL https://pkgs.k8s.io/core:/stable:/v1.29/deb/Release.key | sudo gpg --dearmor -o /etc/apt/keyrings/kubernetes-apt-keyring.gpg
   ```

**Note:** In releases older than Debian 12 and Ubuntu 22.04, directory `/etc/apt/keyrings` does not exist by default, and it should be created before the curl command.

1. Add the appropriate Kubernetes `apt` repository. Please note that this repository have packages only for Kubernetes 1.29; for other Kubernetes minor versions, you need to change the Kubernetes minor version in the URL to match your desired minor version (you should also check that you are reading the documentation for the version of Kubernetes that you plan to install).

   ```shell
   # This overwrites any existing configuration in /etc/apt/sources.list.d/kubernetes.list
   echo 'deb [signed-by=/etc/apt/keyrings/kubernetes-apt-keyring.gpg] https://pkgs.k8s.io/core:/stable:/v1.29/deb/ /' | sudo tee /etc/apt/sources.list.d/kubernetes.list
   ```

2. Update the `apt` package index, install kubelet, kubeadm and kubectl, and pin their version:

   ```shell
   sudo apt-get update
   sudo apt-get install -y kubelet kubeadm kubectl
   sudo apt-mark hold kubelet kubeadm kubectl
   ```

   解除 hold：     apt-mark unhold kubelet kubeadm kubectl

   升级                  apt-get update
   反安装              apt-get purge -y kubelet kubeadm kubectl

3. Enable the kubelet service before running kubeadm:（设置开机启动）

   ```shell
   sudo systemctl enable --now kubelet
   ```

