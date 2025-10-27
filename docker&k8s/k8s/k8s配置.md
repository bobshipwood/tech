[toc]

### 1 中心节点

#### 1 kubeadmin 配置

kubeadm init  \\ 

--cri-socket=unix:///var/run/cri-dockerd.sock \\

 --image-repository=registry.cn-hangzhou.aliyuncs.com/google_containers \\

 --kubernetes-version=v1.29.0 \\

 --pod-network-cidr=10.244.0.0/16 \\  对应着calico的custom-resource.yaml文件

 --service-cidr=10.96.0.0/12 \\

--apisever-advertise-address=192.168.19.129  // mastet节点的地址

可选：--control-plane-endpoint "192.168.1.101:6553" \ 软件负载均衡的地址，端口

```
kubeadm init \
--apiserver-advertise-address=192.168.1.24 \
--control-plane-endpoint="192.168.1.101:6553" \
--upload-certs \
--image-repository=registry.cn-hangzhou.aliyuncs.com/google_containers \
--kubernetes-version=v1.29.0 \
--service-cidr=10.96.0.0/12 \
--pod-network-cidr=10.244.0.0/16 \
--cri-socket=unix:///var/run/cri-dockerd.sock \
————————————————

集群配置           
```

```
集群其他节点配置
 kubeadm join 192.168.1.101:6553 --token o5092n.2juhrog2wjqxtpx3 \
	--discovery-token-ca-cert-hash sha256:1a964286f0a7ce2527d2dce54e47f3f44d423888d679db58c98cc05ebaf8b21d \
	--control-plane --certificate-key 04b07d03a2ac6b03229b78f7402bddaeb74887a3c722846acf845ec733c849f2 \
	--cri-socket=unix:///var/run/cri-dockerd.sock
```

 

#### 2 (options)如果2小时过去，要重新在已经存在的控制中心节点上生成key

```
You can now join any number of the control-plane node running the following command on each as root:

  kubeadm join 192.168.1.101:6553 --token o5092n.2juhrog2wjqxtpx3 \
	--discovery-token-ca-cert-hash sha256:1a964286f0a7ce2527d2dce54e47f3f44d423888d679db58c98cc05ebaf8b21d \
	--control-plane --certificate-key 04b07d03a2ac6b03229b78f7402bddaeb74887a3c722846acf845ec733c849f2

Please note that the certificate-key gives access to cluster sensitive data, keep it secret!
As a safeguard, uploaded-certs will be deleted in two hours; If necessary, you can use
"kubeadm init phase upload-certs --upload-certs" to reload certs afterward.

```

执行这条命令后，执行sudo kubeadm init phase upload-certs --upload-certs，会输出这样的东西

```
[upload-certs] Storing the certificates in Secret "kubeadm-certs" in the "kube-system" Namespace
[upload-certs] Using certificate key:
1a964286f0a7ce2527d2dce54e47f3f44d4...
```

然后在把他重新应用到安装命令

```
sudo kubeadm join 192.168.1.101:6553 --token o5092n.2juhrog2wjqxtpx3 --discovery-token-ca-cert-hash sha256:1a964286f0a7ce2527d2dce54e47f3f44d4... --certificate-key 1a964286f0a7ce2527d2dce54e47f3f44d4...
```

#### 2.5 如果提示token过期(24小时后过期)

```
sudo kubeadm token create

然后把他应用到kubeadm join命令的 --token 处
```



#### 3 安装网络插件（calcio或者flannel）

只有安装了网络插件，工作节点才能正常工作

kubectl create -f https://raw.githubusercontent.com/projectcalico/calico/v3.28.1/manifests/tigera-operator.yaml
kubectl create -f https://raw.githubusercontent.com/projectcalico/calico/v3.28.1/manifests/custom-resources.yaml

验证安装是否完毕

watch kubectl get pods -n calico-system

如果watch 不到，那相当于网络ip段出错，修改custom-resources.yaml文件，改为--pod-network-cidr=10.244.0.0/16

kubectl apply -f  ./custom-resources.yaml 又或者先删除，再create

kubectl delete -f ./flannel.yml

安装flannel（备选，简单）：
kubectl apply -f https://github.com/flannel-io/flannel/releases/latest/download/kube-flannel.yml

#### 4 加入工作节点(重新生成token，但是有可能需要在节点上运行一下kubeadmin init后的环境变量提示)

kubeadm token create --print-join-command

kubeadm join 192.168.19.129:6443 --token oxl3sc.ty3j2ewgjs4sj0dj --discovery-token-ca-cert-hash sha256:7c2886822886ef7c473760fdf56334f32315d5a8d1e90f3d9b84bdbfb6742c90 

后面切记要加入--cri-socket=unix:///var/run/cri-dockerd.sock



#### 5 删除kubedmin

kubeadmin reset --cri-socket=unix:///var/run/cri-dockerd.sock

### 2 工作节点

#### 1 kubeadmin配置

kubeadm join 192.168.19.129:6443 --token wys8aj.77265bkvpsizh62q --discovery-token-ca-cert-hash  sha256:7c2886822886ef7c473760fdf56334f32315d5a8d1e90f3d9b84bdbfb6742c90 --cri-socket=unix:///var/run/cri-dockerd.sock

```
工作节点（集群配置） 
kubeadm join 192.168.1.101:6553 --token o5092n.2juhrog2wjqxtpx3 \
	--discovery-token-ca-cert-hash sha256:1a964286f0a7ce2527d2dce54e47f3f44d423888d679db58c98cc05ebaf8b21d \
	--cri-socket=unix:///var/run/cri-dockerd.sock
```



#### 2 删除kubeadmin

kubeadmin reset --cri-socket=unix:///var/run/cri-dockerd.sock



### 3 平时运维

kubeadm config  images list  查看kubelet的当前版本所需要的镜像，可以提前下载





kubectl top nodes --use-protocol-buffers  查看所有节点占用的资源

kubectl top pods --use-protocol-buffers  查看所有pods占用的资源



kubectl get pods -o wide -A // 所有的namespace下的pod的详细信息
kubectl logs <pod-name> -n kube-system
kubectl describe pod <pod-name> -n kube-system

kubectl get events -n kube-system

进入pod的某个容器执行命令

**kubectl exec -it mypodname -c tomcat -- sh**

编辑某个deployment

kubectl edit deployment my-tomcat



编辑pod配置（更改阿里镜像源，只能临时修改）
kubectl edit pod kube-scheduler-bob -n kube-system
永久修改需要修改 /etc/kubernetes/manifests/kube-scheduler.yaml

#### 1 yaml写法

1 编写yaml的助手 explain后跟资源的类型
**kublctl explain deployment.spec**



2 干跑一次，然后输出相应的yaml文件

**kubectl run my-tomcat --image=tomcat --dry-run=client   -oyaml**

3 **kubectl get XXX -oyaml**





